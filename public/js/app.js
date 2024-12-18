function addEventListeners() {

    let newsEditor = document.querySelector('article.post button.editButton');
    if(newsEditor != null)
      newsEditor.addEventListener('click', openNewsEditor);

    let newsDestroyers = document.querySelectorAll('ul#postsList li button');
    [].forEach.call(newsDestroyers,function(destroyer){
      destroyer.addEventListener('click',deleteNews);
    })
    

    let newsEditCancelButton = document.querySelector('section.postEditForm button.cancelButton');
    if(newsEditCancelButton != null)
      newsEditCancelButton.addEventListener('click',closeNewsEditor);
    
    let newsEditSaveButton = document.querySelector('section.postEditForm button.saveButton');
    if(newsEditSaveButton != null)
      newsEditSaveButton.addEventListener('click',sendUpdatePostRequest);
    

    let saveChangesButton = document.getElementById('saveChanges');
    if (saveChangesButton) {
        saveChangesButton.addEventListener('click', (event) => {
          event.preventDefault();
            updateUserProfile(
                event,
                'editProfileForm',
                'success-message',
                'error-message',
                saveChangesButton.dataset.updateUrl
            );
        });
    }

    let createUserButton = document.getElementById('generateUser');
    if (createUserButton) {
        createUserButton.addEventListener('click', handleCreateUser);
    }
  }
  
  
  function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k){
      return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
  }
  
  function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();
  
    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
  }

  function deleteNews(event){
    
    let id=this.parentElement.getAttribute('id');
    
    sendAjaxRequest('post','/deletePost/'+id,null,deleteNewsHandler)
    event.preventDefault();
  }
  
  function  openNewsEditor(){
    let parent=this.parentElement;
    parent.classList.add('hidden');
    parent.parentElement.querySelector('section.postEditForm').classList.remove('hidden');
    let newTitleInput=parent.parentElement.querySelector('section.postEditForm input#newTitle');
    newTitleInput.focus();
    newTitleInput.setSelectionRange(newTitleInput.value.length,newTitleInput.value.length);
  }

  function closeNewsEditor(event){
    let parent=this.parentElement;
    parent.parentElement.classList.add('hidden');
    parent.parentElement.parentElement.querySelector('article.post').classList.remove('hidden');
    event.preventDefault();
  }

  function sendUpdatePostRequest(event){
    let parent=this.parentElement;
    let postTitle = parent.querySelector('input#newTitle').value;
    let postBody = parent.querySelector('textarea#newBody').value;
    let newTimestamp = new Date().toISOString();
    let id = parent.parentElement.getAttribute('data-id');
    let editForm = parent.parentElement.querySelector('section.postEditForm form'); 
    if(!editForm.checkValidity()){
      
      editForm.reportValidity();
      event.preventDefault();
      return
    }
    
    

    sendAjaxRequest('post','/post/edit/'+id,{title: postTitle, body: postBody, timestamp: newTimestamp },updatePostHandler);
    event.preventDefault();
  }

  function updatePostHandler(){
    
    let post = JSON.parse(this.responseText);
    
    document.querySelector('article.post header.newsTitle h2').innerHTML=post.title;
    document.querySelector('article.post div.newsBody p').innerHTML=post.body;

    document.querySelector('article.post').classList.remove('hidden');
    document.querySelector('section.postEditForm').classList.add('hidden');

  }

  function deleteNewsHandler(){
    
    let res = JSON.parse(this.responseText);
    
    let post = document.getElementById(res.postId);
    
    let message=document.createElement('p');
    
    if(res.success){
      
      message.innerHTML='The post was deleted successfully';

    }
    else{
      
      message.innerHTML='Posts that already have either comments or interations can not be deleted';
      
    }
    
    post.appendChild(message);
    
  }

  
  addEventListeners();
  
  function updateUserProfile(event, formId, successMessageId, errorMessageId, updateUrl) {
    console.log("Save Changes button clicked!");

    const form = document.getElementById(formId);
    if (!form || !(form instanceof HTMLFormElement)) {
        console.error("The form element was not found or is invalid.");
        return;
    }

    const successMessage = document.getElementById(successMessageId);
    const errorMessage = document.getElementById(errorMessageId);

    form.querySelectorAll('.error').forEach(error => {
        error.textContent = '';
        error.style.display = 'none';
    });

    const formData = new FormData(form);

    fetch(updateUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData,
    })
        .then(response => {
            if (!response.ok) {
                if (response.status === 422) {
                    return response.json().then(data => {
                        if (data.errors) throw data.errors;
                        throw new Error('Validation failed without specific errors.');
                    });
                }
                throw new Error('Profile update failed.');
            }
            return response.json();
        })
        .then(data => {
            successMessage.style.display = "block";
            errorMessage.style.display = "none";

            form.querySelector('#username').value = data.username;
            form.querySelector('#email').value = data.email;

            const title = document.querySelector('#title');
            if (title) {
                title.textContent = `Edit ${data.username}'s Profile`;
            }
        })
        .catch(errors => {
            if (typeof errors === 'object') {
                console.error("Validation errors:", errors);

                Object.keys(errors).forEach(field => {
                    const errorSpan = form.querySelector(`#${field}-error`);
                    if (errorSpan) {
                        errorSpan.textContent = errors[field].join(', ');
                        errorSpan.style.display = 'block';
                    }
                });
            } else {
                console.error("Error details:", errors);
                errorMessage.style.display = "block";
                successMessage.style.display = "none";
                errorMessage.innerText = errors || "Unexpected error occurred. Please try again.";
            }
        });
}

function handleCreateUser(event) {

  const form = document.getElementById('adminCreateUser');
  const actionUrl = event.target.dataset.actionUrl;

  if (!form) {
      console.error("Form element not found!");
      return;
  }

  const formData = new FormData(form);

  const errorSpans = form.querySelectorAll('span.error');
  errorSpans.forEach((span) => span.textContent = '');

  fetch(actionUrl, {
      method: 'POST',
      headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json', 
      },
      body: formData,
  })
      .then((response) => {
          if (!response.ok) {
              return response.json().then((data) => {
                  if (data.errors) throw data.errors;
                  throw new Error('Failed to create user');
              });
          }
          return response.json();
      })
      .then((data) => {
          if (data.success) {
              const usersTable = document.querySelector('table tbody');
              const newRow = document.createElement('tr');
              newRow.innerHTML = `
                  <td>${data.user_id}</td>
                  <td>${data.username}</td>
                  <td>${data.email}</td>
                  <td>
                      <a href="/users/${data.user_id}/edit" class="btn btn-sm btn-primary">Edit</a>
                      <button class="btn btn-sm btn-danger" disabled>Delete (Coming Soon)</button>
                  </td>
              `;
              usersTable.appendChild(newRow);

              form.reset();

              const userCreatedMessage = document.getElementById('userCreatedMessage');
              userCreatedMessage.style.display = 'block';
          }
      })
      .catch((errors) => {
          console.error("Validation errors:", errors);

          Object.keys(errors).forEach((field) => {
              const input = form.querySelector(`[name="${field}"]`);
              if (input) {
                  const errorSpan = input.parentElement.querySelector('span.error');
                  if (errorSpan) {
                      errorSpan.textContent = errors[field].join(', ');
                  }
              }
          });
      });
}

function like(postId, alike) {
  let post = document.querySelector(`[data-id="${postId}"]`);
  if (!post) {
      console.error("Post não encontrado para o ID:", postId);
      return;
  }

  let likeCounter = post.querySelector('.qtd-likes');
  if (!likeCounter) {
      console.error("Contador de likes não encontrado no post.");
      return;
  }

  let deslikeCounter = post.querySelector('.qtd-deslikes');
  if (!deslikeCounter) {
      console.error("Contador de deslikes não encontrado no post.");
      return;
  }

  sendAjaxRequest('post', '/post/like', { "post_id": postId, "liked": alike }, (response) => {
      try {
          const data = JSON.parse(response.target.responseText);
          if (data.success) {
              likeCounter.innerText = data.likes;
              deslikeCounter.innerText = data.deslikes;
          } else {
              console.error("Erro ao gravar o like:", data.error);
          }
      } catch (e) {
          console.error("Erro ao processar a resposta do servidor:", e);
      }
  });
}


function voteComment(commentId, liked) {
    sendAjaxRequest('post', '/comment/vote', { comment_id: commentId, liked: liked }, (response) => {
        try {
            const data = JSON.parse(response.target.responseText);
            if (data.success) {
                document.getElementById(`upvotes-${commentId}`).innerText = data.upvotes;
                document.getElementById(`downvotes-${commentId}`).innerText = data.downvotes;
            } else {
                console.error('Error:', data.error);
            }
        } catch (e) {
            console.error('Invalid JSON response:', response.target.responseText);
        }
    });
}


function editComment(commentId) {
    const commentBody = document.getElementById(`comment-body-${commentId}`);
    const editForm = document.getElementById(`edit-comment-form-${commentId}`);
    if (!commentBody || !editForm) {
        console.error("Comentário ou formulário não encontrados.");
        return;
    }
    commentBody.classList.add('hidden');
    editForm.classList.remove('hidden');
}

function cancelEdit(commentId) {
    const commentBody = document.getElementById(`comment-body-${commentId}`);
    const editForm = document.getElementById(`edit-comment-form-${commentId}`);
    if (!commentBody || !editForm) {
        console.error("Comentário ou formulário não encontrados.");
        return;
    }

    editForm.classList.add('hidden');
    commentBody.classList.remove('hidden');
}


document.addEventListener('DOMContentLoaded', function () {
    const postCommentForm = document.getElementById('post-comment-form');

    if (postCommentForm) {
        postCommentForm.addEventListener('submit', function (event) {
            event.preventDefault(); 

            const formData = new FormData(postCommentForm);  
            const url = '/comments/store'; 

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,  
                },
                body: formData  
            })
            .then(response => response.json())  
            .then(data => {
                if (data.success) {
                    addNewCommentToDOM(data.comment);  
                    postCommentForm.reset();  
                } else {
                    console.error('Error:', data.error);  
                }
            })
            .catch(error => console.error('Error:', error));  
        });
    }
});

function addNewCommentToDOM(comment) {
    const commentsSection = document.getElementById('comments');
    const newComment = `
        <article class="comment" data-comment-id="${comment.comment_id}">
            <p id="comment-body-${comment.comment_id}">${comment.body}</p>
            <button class="edit-comment-btn" onclick="editComment(${comment.comment_id})">Edit</button>
            <form id="edit-comment-form-${comment.comment_id}" class="hidden" method="POST">
                <textarea name="body" required>${comment.body}</textarea>
                <button type="button" onclick="saveEditedComment(${comment.comment_id})">Save</button>
                <button type="button" onclick="cancelEdit(${comment.comment_id})">Cancel</button>
            </form>
            <p>Published just now</p>
        </article>
    `;
    commentsSection.insertAdjacentHTML('beforeend', newComment); 
}


function saveEditedComment(commentId) {
    const form = document.querySelector(`#edit-comment-form-${commentId}`);
    const body = form.querySelector('textarea[name="body"]').value;

    fetch(`/comments/update/${commentId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ body: body })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`#comment-body-${commentId}`).innerText = body;
            cancelEdit(commentId);
        } else {
            console.error('Error:', data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}


function toggleReplyForm(commentId) {
    const replyForm = document.getElementById(`reply-form-${commentId}`);
    if (replyForm) {
        replyForm.classList.toggle('hidden');
    }
}

function postReply(parentCommentId) {
    const form = document.getElementById(`reply-form-${parentCommentId}`);
    const body = form.querySelector('textarea[name="body"]').value;

    fetch('/comments/reply', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ body: body, reply_to: parentCommentId }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addReplyToDOM(data.reply, parentCommentId);
                form.reset();
                toggleReplyForm(parentCommentId);
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

function addReplyToDOM(reply, parentCommentId) {
    const parentComment = document.querySelector(`.comment[data-comment-id="${parentCommentId}"]`);
    const repliesSection = parentComment.querySelector('.replies');

    const newReply = `
        <article class="reply" data-reply-id="${reply.comment_id}">
            <p>${reply.body}</p>
            <p><a href="/users/${reply.owner.user_id}">${reply.owner.username}</a> - Published at just now</p>
        </article>
    `;

    if (repliesSection) {
        repliesSection.insertAdjacentHTML('beforeend', newReply);
    } else {
        const repliesDiv = `<div class="replies">${newReply}</div>`;
        parentComment.insertAdjacentHTML('beforeend', repliesDiv);
    }
}

function editReply(replyId) {
    const replyBody = document.getElementById(`reply-body-${replyId}`);
    const editForm = document.getElementById(`edit-reply-form-${replyId}`);
    
    if (replyBody && editForm) {
        replyBody.classList.add('hidden');
        editForm.classList.remove('hidden');
    }
}

function cancelEditReply(replyId) {
    const replyBody = document.getElementById(`reply-body-${replyId}`);
    const editForm = document.getElementById(`edit-reply-form-${replyId}`);
    
    if (replyBody && editForm) {
        editForm.classList.add('hidden');
        replyBody.classList.remove('hidden');
    }
}