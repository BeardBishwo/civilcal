

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center mb-5">
            <h1 class="font-weight-bold text-primary mb-3">⚔️ Engineering Battle Royale</h1>
            <p class="lead text-muted">Compete vs other students (and maybe some ghosts 👻) in real-time!</p>
        </div>
    </div>
    
    <div class="row justify-content-center">
        <!-- Join Game -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-search fa-3x text-info"></i>
                    </div>
                    <h3>Join Battle</h3>
                    <p class="text-muted">Enter a room code to join a friend.</p>
                    
                    <form action="/quiz/lobby/join" method="POST" class="mt-4">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <div class="form-group">
                            <input type="text" name="code" class="form-control form-control-lg text-center text-uppercase" placeholder="ENTER CODE (e.g. A7X92)" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-block btn-lg">Join Room</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Create Game -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-lg h-100 border-primary">
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <i class="fas fa-crown fa-3x text-warning"></i>
                    </div>
                    <h3>Create Lobby</h3>
                    <p class="text-muted">Host a new game and invite others.</p>
                    
                    <form action="/quiz/lobby/create" method="POST" class="mt-4">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                        <!-- Default Exam ID 1 for MVP -->
                        <input type="hidden" name="exam_id" value="1"> 
                        <button type="submit" class="btn btn-primary btn-block btn-lg pulse-button">Create New Room</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pulse-button {
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}
</style>
