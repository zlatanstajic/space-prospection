</main>
<footer id="footer">
    <div class="connect">
        <div>
            <h1>Connect with the developer</h1>
            <div>
                <?php foreach ($socials as $social): ?>
                    <a href="<?=html_escape($social['link'])?>"
                        class="<?=html_escape($social['name'])?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    ><?=html_escape($social['name'])?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="footnote">
        <div>
            <p>
                &copy; 2017&ndash;<?=html_escape($current_year)?>
                <a href="https://www.zlatanstajic.com/" target="_blank" rel="noopener noreferrer">Zlatan Stajić</a>
                &middot; Built with CodeIgniter and SQLite
            </p>
        </div>
    </div>
</footer>
</div>
<script src="<?=base_url('assets/js/mobile.js')?>"></script>
</body>
</html>
