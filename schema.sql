



CREATE SCHEMA IF NOT EXISTS lbaw24104

SET search_path TO lbaw24104;
DROP TYPE IF EXISTS activity_status;
DROP TYPE IF EXISTS report_status;
CREATE TYPE activity_status AS ENUM('active', 'flagged', 'deleted');
CREATE TYPE report_status AS ENUM('resolved', 'pending', 'dismissed');

DROP TABLE IF EXISTS Users CASCADE;
DROP TABLE IF EXISTS Admins CASCADE;
DROP TABLE IF EXISTS Blocked CASCADE;
DROP TABLE IF EXISTS Images CASCADE;
DROP TABLE IF EXISTS Tag CASCADE;
DROP TABLE IF EXISTS Posts CASCADE;
DROP TABLE IF EXISTS Comments CASCADE;
DROP TABLE IF EXISTS followed_tags CASCADE;
DROP TABLE IF EXISTS follwed_users CASCADE;
DROP TABLE IF EXISTS Post_tags CASCADE;
DROP TABLE IF EXISTS InterationComments CASCADE;
DROP TABLE IF EXISTS InterationPosts CASCADE;
DROP TABLE IF EXISTS favorite_posts CASCADE;
DROP TABLE IF EXISTS Report CASCADE;
DROP TABLE IF EXISTS UserReport CASCADE;
DROP TABLE IF EXISTS CommentReport CASCADE;
DROP TABLE IF EXISTS PostReport CASCADE;
DROP TABLE IF EXISTS Block_appeal CASCADE;
DROP TABLE IF EXISTS UpvoteOnPostNotification CASCADE;
DROP TABLE IF EXISTS UpvoteOnCommentNotification CASCADE;
DROP TABLE IF EXISTS CommentNotification CASCADE;


CREATE TABLE Users(
    user_id SERIAL PRIMARY KEY NOT NULL,
    username TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    hash_password TEXT NOT NULL,
    reputation INT DEFAULT 0 NOT NULL
);


CREATE TABLE Admins(
    admin_id INT PRIMARY KEY REFERENCES Users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE TABLE Blocked(
    blocked_id INT PRIMARY KEY REFERENCES Users (user_id) ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE TABLE Images(
    image_id SERIAL PRIMARY KEY NOT NULL,
    user_id INT REFERENCES Users (user_id) ON UPDATE CASCADE ON DELETE CASCADE,
    path TEXT NOT NULL
);


CREATE TABLE Tag(
    tag_id SERIAL PRIMARY KEY NOT NULL,
    name TEXT UNIQUE NOT NULL 
);


CREATE TABLE Posts(
    post_id SERIAL PRIMARY KEY NOT NULL,
    title TEXT NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL CHECK(created_at<=CURRENT_TIMESTAMP),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP CHECK(updated_at>=created_at),
    upvotes INT DEFAULT 0 NOT NULL,
    downvotes INT DEFAULT 0 NOT NULL,
    ownerId INT REFERENCES Users (user_id) ON UPDATE CASCADE
);


CREATE TABLE Comments(
    comment_id SERIAL PRIMARY KEY NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL CHECK(created_at<=CURRENT_TIMESTAMP),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP CHECK(updated_at>=created_at),
    upvotes INT DEFAULT 0 NOT NULL,
    downvotes INT DEFAULT 0 NOT NULL,
    post INT REFERENCES Posts (post_id) ON UPDATE CASCADE,
    reply_to INT REFERENCES Comments (comment_id) ON UPDATE CASCADE,
    ownerId INT REFERENCES Users (user_id) ON UPDATE CASCADE
);


CREATE TABLE followed_tags(
    tagId INT REFERENCES Tag (tag_id) ON UPDATE CASCADE,
    userId INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    PRIMARY KEY (tagId,userId)
);


CREATE TABLE follwed_users(
    userId1 INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    userId2 INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    PRIMARY KEY (userId1,userId2)
);


CREATE TABLE Post_tags(
    post INT REFERENCES Posts (post_id) ON UPDATE CASCADE,
    tag INT REFERENCES Tag (tag_id) ON UPDATE CASCADE,
    PRIMARY KEY (post,tag)
);

--upvotes / downvotes
CREATE TABLE InterationComments(
    userId INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    comment_id INT REFERENCES Comments (comment_id) ON UPDATE CASCADE,
    liked BOOLEAN NOT NULL,
    PRIMARY KEY (userId,comment_id)
);


CREATE TABLE InterationPosts(
    userId INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    postId INT REFERENCES Posts (post_id) ON UPDATE CASCADE,
    liked BOOLEAN NOT NULL,
    PRIMARY KEY (userId,postId)
);


CREATE TABLE favorite_posts(
    userId INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    postId INT REFERENCES Posts (post_id) ON UPDATE CASCADE,
    PRIMARY KEY (userId,postId)
);



CREATE TABLE Report(
    report_id SERIAL PRIMARY KEY NOT NULL,
    reason TEXT NOT NULL,
    created_at TIMESTAMP CHECK(created_at<=CURRENT_TIMESTAMP),
    report_status TEXT NOT NULL DEFAULT 'pending',
    reporter_id INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    reported_user INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    assigned_admin INT REFERENCES Admins (admin_id) ON UPDATE CASCADE
);

CREATE TABLE UserReport(
    rID INT REFERENCES Report (report_id) ON UPDATE CASCADE,
    PRIMARY KEY (rID)
    
);


CREATE TABLE CommentReport(
    rID INT REFERENCES Report (report_id) ON UPDATE CASCADE,
    reported_comment INT REFERENCES Comments (comment_id) ON UPDATE CASCADE,
    PRIMARY KEY (rID)
    
);


CREATE TABLE PostReport(
    rID INT REFERENCES Report (report_id) ON UPDATE CASCADE,
    reported_post INT REFERENCES Posts (post_id) ON UPDATE CASCADE,
    PRIMARY KEY (rID)
    
);


CREATE TABLE Block_appeal(
    report_id INT REFERENCES Report(report_id) ON UPDATE CASCADE,
    user_id INT REFERENCES Users(user_id) ON UPDATE CASCADE,
    reason TEXT NOT NULL,
    PRIMARY KEY (report_id)
);


CREATE TABLE UpvoteOnPostNotification(
    notfId SERIAL PRIMARY KEY,
    is_read BOOLEAN DEFAULT false NOT NULL,
    created_at TIMESTAMP CHECK(created_at<=CURRENT_TIMESTAMP),
    emitter INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    receiver INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    post INT REFERENCES Posts (post_id) ON UPDATE CASCADE
);


CREATE TABLE UpvoteOnCommentNotification(
    notfId SERIAL PRIMARY KEY,
    is_read BOOLEAN DEFAULT false NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP CHECK(created_at<=CURRENT_TIMESTAMP),
    emitter INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    receiver INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    liked_comment INT REFERENCES Comments (comment_id) ON UPDATE CASCADE
);


CREATE TABLE CommentNotification(
    notfId SERIAL PRIMARY KEY,
    is_read BOOLEAN DEFAULT false NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP CHECK(created_at<=CURRENT_TIMESTAMP),
    emitter INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    receiver INT REFERENCES Users (user_id) ON UPDATE CASCADE,
    comment INT REFERENCES Comments (comment_id) ON UPDATE CASCADE
);


CREATE INDEX comment_notification_date ON CommentNotification USING btree (created_at);
CLUSTER CommentNotification USING comment_notification_date;



CREATE INDEX post_tags_tag ON Post_tags USING btree (tag);
CLUSTER Post_tags USING post_tags_tag;



CREATE INDEX comments_post ON Comments(post);



ALTER TABLE Posts
ADD COLUMN tsvectors TSVECTOR;

DROP FUNCTION IF EXISTS post_search_update; 
CREATE FUNCTION post_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = (
            setweight(to_tsvector('english', NEW.title), 'A') ||
            setweight(to_tsvector('english', NEW.body), 'B')
        );
    ELSIF TG_OP = 'UPDATE' THEN
        IF (NEW.title <> OLD.title OR NEW.body <> OLD.body) THEN
            NEW.tsvectors = (
                setweight(to_tsvector('english', NEW.title), 'A') ||
                setweight(to_tsvector('english', NEW.body), 'B')
            );
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER post_search_update
BEFORE INSERT OR UPDATE ON Posts
FOR EACH ROW
EXECUTE PROCEDURE post_search_update();

CREATE INDEX post_content ON Posts USING GIN (tsvectors);



ALTER TABLE Users
ADD COLUMN tsvectors TSVECTOR;

DROP FUNCTION IF EXISTS user_search_update;
CREATE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' OR (TG_OP = 'UPDATE' AND NEW.username <> OLD.username) THEN
        NEW.tsvectors = to_tsvector('english', NEW.username);
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER user_search_update
BEFORE INSERT OR UPDATE ON Users
FOR EACH ROW
EXECUTE PROCEDURE user_search_update();

CREATE INDEX user_username ON Users USING GIN (tsvectors);



ALTER TABLE Comments
ADD COLUMN tsvectors TSVECTOR;

DROP FUNCTION IF EXISTS comment_search_update;
CREATE FUNCTION comment_search_update() RETURNS TRIGGER AS $$
BEGIN
    IF TG_OP = 'INSERT' THEN
        NEW.tsvectors = to_tsvector('english', NEW.body);
    ELSIF TG_OP = 'UPDATE' THEN
        IF (NEW.body <> OLD.body) THEN
            NEW.tsvectors = to_tsvector('english', NEW.body);
        END IF;
    END IF;
    RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER comment_search_update
BEFORE INSERT OR UPDATE ON Comments
FOR EACH ROW
EXECUTE PROCEDURE comment_search_update();

CREATE INDEX comment_content ON Comments USING GIN (tsvectors);

DROP FUNCTION IF EXISTS verify_report;
CREATE FUNCTION verify_report() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF EXISTS (SELECT * FROM Report WHERE NEW.reporter_id = reporter_id AND NEW.reported_user = reported_user AND report_status='pending')
        THEN RAISE EXCEPTION 'Report already done and waiting for resolotion';
        END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER verify_report
    BEFORE INSERT ON Report
    FOR EACH ROW
    EXECUTE PROCEDURE verify_report();

DROP FUNCTION IF EXISTS no_likes_on_own_post;
CREATE FUNCTION no_likes_on_own_post() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF (SELECT ownerId FROM Posts WHERE post_id = NEW.postId) = NEW.userId
        THEN RAISE EXCEPTION 'Users can not interact with their own posts';
        END IF;
        
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER no_likes_on_own_post
    BEFORE INSERT ON InterationPosts
    FOR EACH ROW
    EXECUTE PROCEDURE no_likes_on_own_post();

DROP FUNCTION IF EXISTS no_likes_on_own_comment;
CREATE FUNCTION no_likes_on_own_comment() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF (SELECT ownerId FROM Comments WHERE comment_id = NEW.comment_id) = NEW.userId
        THEN RAISE EXCEPTION 'Users can not interact with their own comments';
        END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER no_likes_on_own_comment
    BEFORE INSERT ON InterationComments
    FOR EACH ROW
    EXECUTE PROCEDURE no_likes_on_own_comment();

DROP FUNCTION IF EXISTS post_like_notification;
CREATE FUNCTION post_like_notification() RETURNS TRIGGER AS
$BODY$
    BEGIN
        INSERT INTO UpvoteOnPostNotification(emitter,receiver,post) VALUES (NEW.userId,(SELECT ownerId FROM Posts WHERE post_id=NEW.postId),NEW.postId);
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER post_like_notification
    AFTER INSERT ON InterationPosts
    FOR EACH ROW
    EXECUTE PROCEDURE post_like_notification();

DROP FUNCTION IF EXISTS comment_like_notification;
CREATE FUNCTION comment_like_notification() RETURNS TRIGGER AS
$BODY$
    BEGIN
        INSERT INTO UpvoteOnCommentNotification(emitter,receiver,liked_comment) VALUES (NEW.userId,(SELECT ownerId FROM Comments WHERE comment_id=NEW.comment_id),NEW.comment_id);
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER comment_like_notification
    AFTER INSERT ON InterationComments
    FOR EACH ROW
    EXECUTE PROCEDURE comment_like_notification();

DROP FUNCTION IF EXISTS comment_notification;
CREATE FUNCTION comment_notification() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF NEW.reply_to IS NOT NULL
        THEN INSERT INTO CommentNotification(emitter,receiver,comment) VALUES (NEW.ownerID,(SELECT ownerId FROM Comments WHERE comment_id=NEW.reply_to),NEW.comment_id);
        ELSE INSERT INTO CommentNotification(emitter,receiver,comment) VALUES (NEW.ownerID,(SELECT ownerId FROM Posts WHERE post_id=NEW.post),NEW.comment_id);
		END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER comment_notification
    AFTER INSERT ON Comments
    FOR EACH ROW
    EXECUTE PROCEDURE comment_notification();

DROP FUNCTION IF EXISTS delete_post_check;
CREATE FUNCTION delete_post_check() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF EXISTS (SELECT * FROM InterationPosts WHERE postId=OLD.post_id) OR EXISTS (SELECT * FROM Comments WHERE post=OLD.post_id)
        THEN RAISE EXCEPTION 'Posts that already has either comments or interations can not be deleted';
        END IF;
        RETURN OLD;
    END
$BODY$
language plpgsql;

CREATE TRIGGER delete_post_check
    BEFORE DELETE ON Posts
    FOR EACH ROW
    EXECUTE PROCEDURE delete_post_check();


DROP FUNCTION IF EXISTS delete_comment_check;
CREATE FUNCTION delete_comment_check() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF EXISTS (SELECT * FROM InterationComments WHERE comment_id=OLD.comment_id) OR EXISTS (SELECT * FROM Comments WHERE reply_to=OLD.comment_id)
        THEN RAISE EXCEPTION 'Comments that already have either replys or interations can not be deleted';
        END IF;
        RETURN OLD;
    END
$BODY$
language plpgsql;

CREATE TRIGGER delete_comment_check
    BEFORE DELETE ON Comments
    FOR EACH ROW
    EXECUTE PROCEDURE delete_comment_check();



DROP FUNCTION IF EXISTS update_reputation_posts;
CREATE FUNCTION update_reputation_posts() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF NEW.liked THEN UPDATE Posts SET upvotes = upvotes + 1 WHERE post_id = NEW.postId; UPDATE Users SET reputation = reputation+1 WHERE user_id=NEW.userId;
        ELSE UPDATE Posts SET downvotes = downvotes + 1 WHERE post_id = NEW.postId; UPDATE Users SET reputation = reputation - 1 WHERE user_id=NEW.userId;
        END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER update_reputation_posts
    AFTER INSERT ON InterationPosts
    FOR EACH ROW
    EXECUTE PROCEDURE update_reputation_posts();

DROP FUNCTION IF EXISTS update_reputation_comments;
CREATE FUNCTION update_reputation_comments() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF NEW.liked THEN UPDATE Comments SET upvotes = upvotes + 1 WHERE comment_id = NEW.comment_id; UPDATE Users SET reputation = reputation+1 WHERE user_id=NEW.userId;
        ELSE UPDATE Comments SET downvotes = downvotes + 1 WHERE comment_id = NEW.comment_id; UPDATE Users SET reputation = reputation - 1 WHERE user_id=NEW.userId;
        END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER update_reputation_comments
    AFTER INSERT ON InterationComments
    FOR EACH ROW
    EXECUTE PROCEDURE update_reputation_comments();

DROP FUNCTION IF EXISTS update_reputation_posts_removed_like;
CREATE FUNCTION update_reputation_posts_removed_like() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF OLD.liked THEN UPDATE Posts SET upvotes = upvotes - 1 WHERE post_id = OLD.postId; UPDATE Users SET reputation = reputation-1 WHERE user_id=OLD.userId;
        ELSE UPDATE Post SET downvotes = downvotes - 1 WHERE post_id = OLD.postId; UPDATE Users SET reputation = reputation + 1 WHERE user_id=OLD.userId;
        END IF;
        RETURN OLD;
    END
$BODY$
language plpgsql;

CREATE TRIGGER update_reputation_posts_removed_like
    BEFORE DELETE ON InterationPosts
    FOR EACH ROW
    EXECUTE PROCEDURE update_reputation_posts_removed_like();

DROP FUNCTION IF EXISTS update_reputation_comments_removed_like;
CREATE FUNCTION update_reputation_comments_removed_like() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF OLD.liked THEN UPDATE Comments SET upvotes = upvotes - 1 WHERE comment_id = OLD.comment_id; UPDATE Users SET reputation = reputation-1 WHERE user_id=OLD.userId;
        ELSE UPDATE Comments SET downvotes = downvotes - 1 WHERE comment_id = OLD.comment_id; UPDATE Users SET reputation = reputation + 1 WHERE user_id=OLD.userId;
        END IF;
        RETURN OLD;
    END
$BODY$
language plpgsql;

CREATE TRIGGER update_reputation_comments_removed_like
    BEFORE DELETE ON InterationComments
    FOR EACH ROW
    EXECUTE PROCEDURE update_reputation_comments_removed_like();


DROP FUNCTION IF EXISTS block_appeal_verify;
CREATE FUNCTION block_appeal_verify() RETURNS TRIGGER AS
$BODY$
    BEGIN
        IF EXISTS(SELECT * FROM Report WHERE report_id=NEW.report_id AND report_status='pending')
        THEN RAISE EXCEPTION 'Report still pending wait until resulotion';
        END IF;
        IF EXISTS(SELECT * FROM Report WHERE report_id=NEW.report_id AND (report_status='resolved' OR report_status='dismissed'))
        THEN UPDATE Report SET report_status='pending' WHERE report_id=NEW.report_id;
        END IF;
        RETURN NEW;
    END
$BODY$
language plpgsql; 

CREATE TRIGGER block_appeal_verify
    BEFORE INSERT ON Block_appeal
    FOR EACH ROW
    EXECUTE PROCEDURE block_appeal_verify();
 

DROP FUNCTION IF EXISTS give_admin_to_report;
CREATE FUNCTION give_admin_to_report() RETURNS TRIGGER AS
$BODY$
    BEGIN
        
		UPDATE Report SET assigned_admin = (SELECT admin_id FROM Admins WHERE admin_id = (SELECT assigned_admin FROM Report WHERE assigned_admin IN (SELECT admin_id FROM Admins) GROUP BY assigned_admin ORDER BY COUNT(*) ASC LIMIT 1)) WHERE report_id=NEW.report_id;
        RETURN NEW;
    END
$BODY$
language plpgsql;

CREATE TRIGGER give_admin_to_report
    AFTER INSERT ON Report
    FOR EACH ROW
    EXECUTE PROCEDURE give_admin_to_report();