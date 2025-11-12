<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <h1 class="mb-4">Create Account</h1>
      <form id="registerForm" method="post" action="<?= $viewHelper->url('register') ?>">
        <?php $viewHelper->csrfField(); ?>

        <div class="row g-3">
          <div class="col-md-6">
            <label for="first_name" class="form-label">First name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
          </div>
          <div class="col-md-6">
            <label for="last_name" class="form-label">Last name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
          </div>
        </div>

        <div class="mt-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="mt-3">
          <label for="username" class="form-label">Username (optional)</label>
          <input type="text" class="form-control" id="username" name="username" autocomplete="username">
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-6">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" minlength="8" required>
          </div>
          <div class="col-md-6">
            <label for="confirm_password" class="form-label">Confirm password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
          </div>
        </div>

        <div class="form-check my-3">
          <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
          <label class="form-check-label" for="terms">I agree to the Terms and Privacy Policy</label>
        </div>

        <button type="submit" class="btn btn-primary">Create Account</button>
        <a class="btn btn-link" href="<?= $viewHelper->url('login') ?>">Already have an account? Sign in</a>

        <div id="registerResult" class="alert mt-3 d-none"></div>
      </form>
    </div>
  </div>
</div>
<script>
(function(){
  const form = document.getElementById('registerForm');
  const result = document.getElementById('registerResult');
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
      setTimeout(() => { window.location.href = data.redirect; }, 500);
    }
  });
})();
</script>
