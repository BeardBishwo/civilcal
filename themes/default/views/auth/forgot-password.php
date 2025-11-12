<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h1 class="mb-4">Forgot Password</h1>
      <p class="text-muted">Enter your email address. If an account exists, we will send a reset link.</p>
      <form id="forgotForm" method="post" action="<?= $viewHelper->url('forgot-password') ?>">
        <?php $viewHelper->csrfField(); ?>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Send Reset Link</button>
        <a class="btn btn-link" href="<?= $viewHelper->url('login') ?>">Back to Sign in</a>
        <div id="forgotResult" class="alert mt-3 d-none"></div>
      </form>
    </div>
  </div>
</div>
<script>
(function(){
  const form = document.getElementById('forgotForm');
  const result = document.getElementById('forgotResult');
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
    result.textContent = data.message || (data.success ? 'If an account exists with this email, a password reset link has been sent.' : 'Request failed');
  });
})();
</script>
