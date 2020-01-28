<header>
    <!-- Menü-Navigation -->
    <section class="navigation">
            <div class="nav-container">
                <div class="brand">
                    <a href="/overview/">Digitales Klassenbuch</a>
                </div>
                <nav>
                    <div class="nav-mobile">
                        <a id="nav-toggle" href="#!">
                            <span></span>
                        </a>
                    </div>
                    <ul class="nav-list">
                        <li><a href="/overview/">Übersicht</a></li>
                        <li><a href="/timetables/">Stundenpläne</a></li>
                        <li>
                            <a href="#!">Schule</a>
                            <ul class="nav-dropdown">
                                <li><a href="/school/students/">Schüler</a></li>
                                <li><a href="/school/teachers/">Lehrer</a></li>
                                <li><a href="/school/classes/">Klassen</a></li>
                                <li><a href="/school/subjects/">Fächer</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#!">Verwalten</a>
                            <ul class="nav-dropdown">
                                <li><a href="/management/users/">Nutzer</a></li>
                                <li><a href="/management/rooms/">Räume</a></li>
                                <li><a href="/management/absence-types/">Fehlzeit-Typen</a></li>
                                <li><a href="/management/logging/">Protokoll</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="#!"><?php echo($_SESSION['USERNAME']); ?></a>
                            <ul class="nav-dropdown">
                                <li><a href="/management/users/user/?id=<?php 
                                include_once(INC_ROOT . 'db-helper.php'); 
                                echo(get_user($mysqli)['Id']) 
                                ?>">Einstellungen</a></li>
                            </ul>
                        </li>
                        <li><a href="/auth/logout.php">Logout</a></li>
                    </ul>
                </nav>
            </div>
        </section>
        <div class="section-box">
            <p id="section-title">Sie sind hier: <?php echo($section); ?></p>
        </div>
</header>