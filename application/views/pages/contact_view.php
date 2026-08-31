<div id="body">
    <div class="header">
        <div class="contact">
            <h1>Contact</h1>
            <h2>Start a conversation about the project</h2>
            <?php if ($static_export): ?>
                <p class="form-status static-demo">
                    This is a read-only demonstration, so the contact form is not available.
                    Visit the
                    <a href="https://github.com/zlatanstajic/space-prospection">project repository</a>
                    to explore the implementation.
                </p>
            <?php else: ?>
            <form id="submit-message" action="<?=site_url('submit-message')?>" method="post">
                <label for="contact-name">Name</label>
                <input id="contact-name"
                    type="text"
                    name="name"
                    class="contact-input"
                    autocomplete="name"
                    maxlength="80"
                    required
                >
                <label for="contact-email">Email address</label>
                <input id="contact-email"
                    type="email"
                    name="email"
                    class="contact-input"
                    autocomplete="email"
                    required
                >
                <label for="contact-subject">Subject</label>
                <input id="contact-subject"
                    type="text"
                    name="subject"
                    class="contact-input"
                    maxlength="120"
                    required
                >
                <label for="contact-message">Message</label>
                <textarea id="contact-message"
                    name="message"
                    class="contact-input"
                    cols="50"
                    rows="7"
                    maxlength="160"
                    required
                ></textarea>
                <button type="submit" id="submit">Send message</button>
            </form>
            <p id="form-status" class="form-status" role="status" aria-live="polite"></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (!$static_export): ?>
<script src="<?=base_url('assets/js/contact.js')?>"></script>
<?php endif; ?>
