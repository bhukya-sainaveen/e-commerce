
<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <a class="navbar-brand text-white" href="https://sainaveen.great-site.net/oakspro/">E-Commerce Demo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link text-white" href="https://sainaveen.great-site.net/oakspro/services/orders">Orders<span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="https://sainaveen.great-site.net/oakspro/services/cart">Cart</a>
            </li>
            <?php if (isset($_SESSION["name"])): ?>
                <!-- If user is logged in -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="#"><?php echo "Hi ".explode(' ',$_SESSION["name"])[0]; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://sainaveen.great-site.net/oakspro/auth/logout">Logout</a>
                </li>
            <?php else: ?>
                <!-- If user is not logged in -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://sainaveen.great-site.net/oakspro/auth/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="https://sainaveen.great-site.net/oakspro/auth/register">Register</a>
                </li>
            <?php endif; ?>
        </ul>
        
        <!-- Implement the below search button later -->
        <!--
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-dark my-2 my-sm-0" type="submit">Search</button>
        </form>
        -->
    </div>
</nav>
