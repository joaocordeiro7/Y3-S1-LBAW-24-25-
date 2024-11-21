insert into Users (username, email, hash_password, reputation) values ('dcarlens0', 'rmaven0@dailymotion.com', '$2a$04$oNIr9p3lWIZ20hWbLsxVlOJ.ellKqxNgLobqlsgUddstp1cESpcV.', 39);
insert into Users (username, email, hash_password, reputation) values ('mclarycott1', 'cweld1@infoseek.co.jp', '$2a$04$KQ15gdmEq.sRpMCYtM5iueqjoXwPBJkGrwLPlBlqZEaBh6oZLhXVO', 196);
insert into Users (username, email, hash_password, reputation) values ('qmounce2', 'shanne2@businesswire.com', '$2a$04$qw.eocfmfc4L2noZlpilQuNfbLskiEppSFHx95KbMbF3gW4jd/Hum', 3);
insert into Users (username, email, hash_password, reputation) values ('llyles3', 'lskidmore3@msn.com', '$2a$04$1GwhWYGtK7HFQu91RcsMQ.HMXhGtvTwowyogEoKK7A350JkdVjsPy', 93);
insert into Users (username, email, hash_password, reputation) values ('ehampson4', 'jayers4@weather.com', '$2a$04$3wc8iEofDu8HjjM4DwF4i.tJZuBD5qCIv3e1waDDO.RMAJkeatHkS', 195);

INSERT INTO Admins (admin_id) VALUES
(1),
(2),
(3),
(4),
(5);

INSERT INTO Tag (name) VALUES
('Technology'),
('Science'),
('Health'),
('Education'),
('Travel'),
('Food');



INSERT INTO Posts (title, body, upvotes, downvotes, ownerId, created_at, updated_at) VALUES
('The Future of AI', 'Exploring how artificial intelligence will change our world...', 120, 10, 1, '2024-01-15 08:00:00', NULL),
('Top 10 Healthy Foods', 'A guide to the best foods for a healthy lifestyle...', 85, 2, 2, '2024-01-18 09:15:00', NULL),
('Travel Tips for 2024', 'Get ready for new destinations and experiences...', 60, 5, 3, '2024-01-20 10:30:00', '2024-02-01 12:45:00'),
('Finance Hacks', 'Simple ways to manage your finances...', 90, 7, 4, '2024-01-22 11:00:00', NULL),
('Digital Learning Trends', 'What you need to know about digital education...', 110, 3, 5, '2024-01-25 14:10:00', NULL);



INSERT INTO Comments (body, upvotes, downvotes, post, reply_to, ownerId, created_at, updated_at) VALUES
('Interesting perspective!', 15, 2, 1, NULL, 6, '2024-01-15 09:00:00', NULL),
('I disagree with this point...', 8, 10, 2, NULL, 7, '2024-01-18 09:30:00', NULL),
('Well explained, thanks!', 20, 1, 3, NULL, 8, '2024-01-20 10:45:00', '2024-01-25 11:00:00'),
('Great read!', 12, 0, 4, NULL, 9, '2024-01-22 11:00:00', NULL),
('Could you elaborate on this?', 10, 0, 5, NULL, 10, '2024-01-25 13:10:00', NULL);


INSERT INTO InterationPosts (userId, postId, liked) VALUES
(22, 1, true),
(2, 1, false),
(3, 2, true),
(4, 3, true),
(5, 4, false);


INSERT INTO InterationComments (userId, comment_id, liked) VALUES
(22, 1, true),
(2, 2, false),
(3, 3, true),
(4, 4, true),
(5, 5, false),
(6, 6, true),
(7, 7, false);

INSERT INTO followed_tags (tagId, userId) VALUES
(1, 22),
(2, 3),
(3, 4),
(4, 5),
(5, 6),
(6, 8);
 
 
INSERT INTO follwed_users (userId1, userId2) VALUES
(22, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6),
(6, 7);
 

INSERT INTO favorite_posts (userId, postId) VALUES
(55, 1),
(22, 3),
(23, 5),
(41, 7),
(15, 9),
(26, 2);

 

INSERT INTO Post_tags (post, tag) VALUES
(1, 1),
(1, 3),
(2, 2),
(2, 4),
(3, 5),
(4, 1);

 

