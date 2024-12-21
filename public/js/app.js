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
    
    let followButton = document.querySelector('div.profile-container button.follow');
    if(followButton != null)
      followButton.addEventListener('click',sendFollowRequest);

    let unfollowButton = document.querySelector('div.profile-container button.unfollow');
    if(unfollowButton != null)
      unfollowButton.addEventListener('click',sendUnfollowRequest);

    let notfMeunuButton = document.querySelector('span.dropdown');
    if(notfMeunuButton !=null)
      notfMeunuButton.addEventListener('click',openNotfMenu);

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

    let acceptProposalButtons = document.querySelectorAll('.accept-proposal');
    if (acceptProposalButtons) {
        acceptProposalButtons.forEach(button => {
            button.addEventListener('click', handleAcceptProposal);
        });
    }

    let discardProposalButtons = document.querySelectorAll('.discard-proposal');
    if (discardProposalButtons) {
        discardProposalButtons.forEach(button => {
            button.addEventListener('click', handleDiscardProposal);
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


  function openNotfMenu(){
    let notfs=document.getElementById('notfs');
    console.log(notfs);
    notfs.classList.add('visible');
    console.log(notfs);
    this.removeEventListener('click', openNotfMenu);
    this.addEventListener('click',closeNotfMenu);
  }

  function closeNotfMenu(){
    let notfs=document.getElementById('notfs');
    notfs.classList.remove('visible');
    this.removeEventListener('click', closeNotfMenu);
    this.addEventListener('click',openNotfMenu);
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
    document.querySelector('article.post div.newsBody pre').innerHTML=post.body;

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

  function sendFollowRequest(event){
    let id = this.getAttribute('data-id');
    sendAjaxRequest('post','/api/follow/'+id,null,followHandler)
    event.preventDefault();
  }

  function followHandler(){
    let res = JSON.parse(this.responseText);
    
    let button=document.querySelector('div.profile-container .follow');
    if(res.fail){
      //adicionar msg de erro
    }
    else{
      
      button.classList.add('unfollow');
      button.classList.remove('follow');
      button.innerHTML="unfollow";
      button.removeEventListener('click', sendFollowRequest);
      button.addEventListener('click',sendUnfollowRequest);
    }
    
    
  }


  function sendUnfollowRequest(event){
    let id = this.getAttribute('data-id');
    sendAjaxRequest('post','/api/unfollow/'+id,null,unfollowHandler)
    event.preventDefault();
  }

  function unfollowHandler(){
    let res = JSON.parse(this.responseText);
    
    let button=document.querySelector('div.profile-container .unfollow');
    if(res.fail){
      //adicionar msg de erro
    }
    else{
      
      button.classList.add('follow');
      button.classList.remove('unfollow');
      button.innerHTML="follow";
      button.removeEventListener('click', sendUnfollowRequest);
      button.addEventListener('click',sendFollowRequest);
    }
    
    
  }
  
  addEventListeners();

  let lastIdChecked=0;
  notfList=document.querySelector('ol#notfs');
  lastNotf=document.querySelector('ol#notfs :first-child');

  if(notfList != null){

  
    notfs = document.querySelectorAll('ol#notfs li button');
    [].forEach.call(notfs,function(notf){
      notf.addEventListener('click',readNews);
    })

    function readNews(event){
      nid = this.getAttribute('data-id');
      ntype = this.getAttribute('data-type');
      console.log(nid);
      console.log(ntype);

      sendAjaxRequest('post','/api/readNotf',{id: nid,type: ntype },readHandler(nid,ntype));
      event.preventDefault();
    }

    function readHandler(id,type){
      notfs=document.querySelector('ol#notfs');
      read_ntof = notfs.querySelector(`button[data-id="${id}"][data-type="${type}"]`).parentElement;
      notfs.removeChild(read_ntof);
    }
    
    lastIdChecked=lastNotf.getAttribute('data-id');

    function checkForNotf(){
      sendAjaxRequest('post','/api/checkNotf',{lastId: lastIdChecked},NotfUpdate);
      console.log({lastId: lastIdChecked});
    }

    function NotfUpdate(){
      let res = JSON.parse(this.responseText);

      if(res.length>0){
        for (let index = 0; index < res.length; index++) {
          const notf = res[index];
          // add to list
          lentry=document.createElement('li');
          lentry.setAttribute('data-id', notf.created_at);
          read_button=document.createElement('button');
          let par = document.createElement('p');
          read_button.addEventListener('click',readNews);

          if(notf.post){
            link = document.createElement('a');
            link.innerHTML='see';
            link.href='/post/'+notf.post;

            par = document.createElement('p')
            par.innerHTML=notf.emitter+' liked one of your posts '+link;

            read_button.setAttribute('data-type','like_post');
            read_button.setAttribute('data-id',notf.notfid);

            lentry.appendChild(par);
            lentry.appendChild(read_button);
            

          }

          if(notf.liked_comment){
            
            
            par.innerHTML='A user liked one of your comments';
            read_button.setAttribute('data-type','like_comment');
            read_button.setAttribute('data-id',notf.notfid);
            
            
            lentry.appendChild(par);
            lentry.appendChild(read_button);
          }

          if(notf.comment){
            

            par.innerHTML='A user comment on one of your posts';
            read_button.setAttribute('data-type','comment');
            read_button.setAttribute('data-id',notf.notfid);
            
            
            lentry.appendChild(par);
            lentry.appendChild(read_button);
          }
          
          notfList.insertBefore(lentry,notfList.firstChild);
          
          
        }
        lastIdChecked=res[0].created_at;
        //give warning
      }
      
      console.log(res);
    }
    setInterval(checkForNotf,10000);
  }
  function getMore(route,rbody,handler){
    sendAjaxRequest('post',route,rbody,handler);
  }
  //infinite scrolling for posts

  let page = 0;
  let pageResults = 0;
  let loading = false;
  let posts = document.querySelector('section#posts');
  let search= document.querySelector('input#search');
  console.log(posts);

  function MorePostsHandler(){
    let res = JSON.parse(this.responseText);
    loading=false
    console.log(res);
    if(res['data'].length>0){
      console.log('a');
      for(let i = 0; i<res['data'].length; i++){
        let postArticle = document.createElement('article');
        postArticle.classList.add('post');

        const post = res['data'][i];

        let postTitle = post['title'];
        let postBody = post['body'];
        let postId = post['post_id'];

        let newTitle = document.createElement('header');
        newTitle.append(document.createElement('h2').innerHTML=postTitle);

        let newBody = document.createElement('p');
        let postPreview = postBody.split(' ');
        if (postPreview.length>25){
          newBody.innerHTML=postPreview.slice(0,25).join(' ') + '...';
        }
        else{
          newBody.innerHTML=postBody;
        }

        let urlForPost = document.createElement('a');
        urlForPost.href='/post/'+postId;
        urlForPost.innerHTML='Read More'

        postArticle.append(newTitle);
        postArticle.append(newBody);
        postArticle.append(urlForPost);

        posts.append(postArticle);
      }

    }
    else{
      
      //window.removeEventListener('scroll');
    }
  }

  function loadMorePosts(){
    loading=true;
    let searchValue = search.value;
    if(searchValue==""){
      getMore('/api/getMorePosts',{page: page,search:searchValue},MorePostsHandler);
      page++;
    }
    else{
      console.log(searchValue)
      getMore('/api/getMorePosts',{page: pageResults,search:searchValue},MorePostsHandler);
      pageResults++;
    }
    
    
  }

  if(posts != null){
    
    window.addEventListener('scroll',function(){
      
      if((window.innerHeight + window.scrollY) >= (document.body.offsetHeight-2) && !loading){
        loadMorePosts();
      }
    })
    

  }

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

function openProposalForm() {
    const modal = document.getElementById('proposalModal');
    if (modal) {
        modal.style.display = 'block';
        modal.style.opacity = '1';
        modal.style.transition = 'opacity 0.3s ease';
    }
}

function closeProposalForm() {
    const modal = document.getElementById('proposalModal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300); 
    }
}

function handleAcceptProposal(event) {
    const acceptUrl = event.target.dataset.acceptUrl;

    console.log('Accept URL:', acceptUrl);

    if (!confirm('Are you sure you want to accept this topic?')) {
        return;
    }

    fetch(acceptUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to accept the proposal.');
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while accepting the proposal.');
        });
}

function handleDiscardProposal(event) {
    const discardUrl = event.target.dataset.discardUrl;

    console.log('Discard URL:', discardUrl);

    if (!confirm('Are you sure you want to discard this topic?')) {
        return;
    }

    fetch(discardUrl, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to discard the proposal.');
            }
            return response.json();
        })
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while discarding the proposal.');
        });
}