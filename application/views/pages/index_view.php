<div id="body" class="home">
    <div class="header">
        <div>
            <img src="<?=base_url('assets/images/satellite.png')?>" alt="" class="satellite">
            <h1>CodeIgniter</h1>
            <h2>MVC showcase</h2>
            <a class="more" href="<?=base_url('about')?>">Explore the build</a>
            <h3>Database-driven projects</h3>
            <ul>
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <li>
                        <a href="<?=base_url('projects')?>">
                            <img src="<?=base_url('assets/images/project-image' . $i . '.jpg')?>"
                                alt="View the space projects collection"
                            >
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
    <div class="body">
        <div>
            <h1>Built to demonstrate the full stack</h1>
            <p>
                A compact PHP 8.1 application by Zlatan Stajić, combining CodeIgniter MVC,
                a portable SQLite data layer, server-side validation, responsive views,
                and automated quality checks in one approachable codebase.
            </p>
        </div>
    </div>
</div>
