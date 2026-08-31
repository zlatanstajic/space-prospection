<div id="body" class="about">
    <div class="header">
        <div>
            <h1>About the project</h1>
            <h2>A focused CodeIgniter showcase</h2>
            <p>
                Space Prospection turns a fictional space-exploration brief into a complete,
                server-rendered application. The project is intentionally small enough to understand
                quickly while still demonstrating the boundaries of a practical MVC architecture.
            </p>
        </div>
    </div>
    <div class="body">
        <div>
            <img src="<?=base_url('assets/images/earth-satellite.jpg')?>" alt="Satellite orbiting Earth">
            <h2>Clear MVC responsibilities</h2>
            <p>
                A single controller coordinates page requests and validation, the model owns every
                database query, and focused views render escaped data. Shared navigation and social
                links come from SQLite and flow through the same reusable page chrome.
            </p>
        </div>
    </div>
    <div class="footer">
        <div>
            <img src="<?=base_url('assets/images/space-shuttle.png')?>" alt="Space shuttle illustration">
            <h2>Portable by design</h2>
            <p>
                The committed SQLite database removes the need for a separate database server, and
                Composer plus Make provide repeatable setup, local serving, style checks, tests, and
                coverage commands. A fresh clone is ready to explore with very little ceremony.
            </p>
        </div>
    </div>
    <div class="section">
        <div>
            <h2>Quality is part of the example</h2>
            <p>
                PHPUnit exercises the controller and model behavior, while PHP_CodeSniffer keeps the
                application consistent. The repository also documents its architecture and development
                workflow so the code can serve as both a portfolio piece and a learning resource.
            </p>
        </div>
    </div>
</div>
