<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h1 class="mb-4">Sign In</h1>
      <form id="loginForm" method="post" action="<?= $viewHelper->url('login') ?>">
        <?php $viewHelper->csrfField(); ?>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" id="remember" name="remember">
          <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary">Sign In</button>
        <a class="btn btn-link" href="<?= $viewHelper->url('forgot-password') ?>">Forgot password?</a>
        <div id="loginResult" class="alert mt-3 d-none"></div>
      </form>
    </div>
  </div>
</div>
<script>
(function(){
  const form = document.getElementById('loginForm');
  const result = document.getElementById('loginResult');
  form.addEventListener('submit', async function(e){
    e.preventDefault();
    result.classList.add('d-none');
    const formData = new FormData(form);
    const payload = new URLSearchParams(formData);
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrf = meta ? meta.getAttribute('content') : (formData.get('csrf_token') || '');
    const res = await fetch(form.action, {
      method: 'POST',
      headers: { 'X-CSRF-Token': csrf, 'Accept': 'application/json' },
      credentials: 'include',
      body: payload
    });
    const data = await res.json().catch(() => ({ success:false, message:'Invalid server response' }));
    result.classList.remove('d-none');
    result.classList.toggle('alert-success', !!data.success);
    result.classList.toggle('alert-danger', !data.success);
    result.textContent = data.message || (data.success ? 'Success' : 'Failed');
    if (data.success && data.redirect) {
      setTimeout(() => { window.location.href = data.redirect; }, 400);
    }
  });
})();
</script>
