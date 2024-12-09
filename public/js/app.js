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

    let deleteAccountButtons = document.querySelectorAll('button.delete-account');
    if (deleteAccountButtons) {
        deleteAccountButtons.forEach(button => {
            button.addEventListener('click', handleDeleteAccount);
        });
    }

    let blockUserButtons = document.querySelectorAll('.block-user');
    if(blockUserButtons) {
        blockUserButtons.forEach(button => {
            button.addEventListener('click', handleBlockUser);
        });
    }

    let unblockUserButtons = document.querySelectorAll('.unblock-user');
    if(unblockUserButtons) {
        unblockUserButtons.forEach(button => {
            button.addEventListener('click', handleUnblockUser);
        });
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

        const profilePicture = document.getElementById('profile-picture-display');
        if (profilePicture && data.image_path) {
            profilePicture.src = data.image_path + '?' + new Date().getTime(); // Cache-busting
        }

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
                        <a href="/users/${data.user_id}" class="btn btn-sm btn-primary">View</a>
                        <button type="button" class="btn btn-danger delete-account"
                            data-delete-url="/admin/delete/${data.user_id}"
                            data-context="admin">
                            Delete
                        </button>
                  </td>
              `;
              usersTable.appendChild(newRow);

              const deleteButton = newRow.querySelector('.delete-account');
              deleteButton.addEventListener('click', handleDeleteAccount);
  

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

function handleDeleteAccount(event) {
    const deleteUrl = event.target.dataset.deleteUrl;
    const context = event.target.dataset.context;

    if (!confirm('Are you sure you want to delete your account? This action is irreversible.')) {
        return;
    }

    fetch(deleteUrl, {
        method: 'DELETE', 
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Failed to delete account.');
        }
        return response.json();
    })
    .then((data) => {
            alert(data.message || 'Account deleted successfully.');

            if (context !== 'admin') {
                window.location.href = '/';
            } else {
                event.target.closest('tr').remove();
            }
        })
    .catch((error) => {
        console.error('Error:', error);
        alert('An error occurred while deleting the account. Please try again.');
    });
}

function handleBlockUser(event) {
    const blockUrl = event.target.dataset.blockUrl;

    console.log('Block URL:', blockUrl);

    if(!confirm('Are you sure you want to block this user?')) {
        return;
    }

    fetch(blockUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to block user.');
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        location.reload(); 
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while blocking the user.');
    });
}

function handleUnblockUser(event) {
    const unblockUrl = event.target.dataset.unblockUrl;

    fetch(unblockUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to unblock user.');
        }
        return response.json();
    })
    .then(data => {
        alert(data.message);
        location.reload(); 
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while unblocking the user.');
    });
}

