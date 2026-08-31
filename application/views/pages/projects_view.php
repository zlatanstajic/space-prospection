<div id="body">
    <div class="header">
        <div>
            <h1>Space projects</h1>
            <h2>Content powered by SQLite</h2>
            <p>
                These mission profiles are queried by the model, ordered by entry date,
                and rendered through an escaped CodeIgniter view.
            </p>
            <ul>
                <?php foreach ($projects as $project): ?>
                    <li>
                        <span class="project-image">
                            <img src="<?=base_url('assets/images/projects/' . $project['image'])?>"
                                alt="<?=html_escape($project['title'])?>"
                            >
                        </span>
                        <div>
                            <h1><?=html_escape($project['title'])?></h1>
                            <p><?=html_escape($project['description'])?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
