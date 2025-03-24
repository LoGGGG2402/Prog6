<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-graduation-cap me-2"></i>Classroom Management System</h5>
                <p class="small mb-0">A comprehensive solution for educational institutions to manage classroom activities, assignments and challenges.</p>
            </div>
            <div class="col-md-3">
                <h6>Quick Links</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ route('home') }}" class="text-light">Home</a></li>
                    <li><a href="{{ route('assignments.index') }}" class="text-light">Assignments</a></li>
                    <li><a href="{{ route('challenges.index') }}" class="text-light">Challenges</a></li>
                </ul>
            </div>
            <div class="col-md-3 text-md-end">
                <h6>Contact</h6>
                <p class="small mb-0">Email: support@classroom.example</p>
                <p class="small mb-0">Phone: (123) 456-7890</p>
            </div>
        </div>
        <div class="text-center mt-3 pt-3 border-top border-secondary">
            <p class="mb-0 small">&copy; {{ date('Y') }} Classroom Management System. All rights reserved.</p>
        </div>
    </div>
</footer>
