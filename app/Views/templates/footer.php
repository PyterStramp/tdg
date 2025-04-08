<!-- Aquí termina el contenido de la página -->
</div> <!-- Fin del container -->
    
    <?php if (isUserLoggedIn()): ?>
    </div> <!-- Fin de main-content -->
    
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?>
                </div>
                
                <div class="footer-links">
                    <a href="<?php echo baseUrl('info/help'); ?>">Ayuda</a>
                    <a href="<?php echo baseUrl('info/about'); ?>">Acerca de</a>
                </div>
            </div>
        </div>
    </footer>
    <?php else: ?>
    <footer class="login-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> - <?php echo APP_NAME; ?></p>
            <p>PHP Mailer 6.9.3</p>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- Scripts al final del documento -->
    <?php if (isset($footerScripts)): ?>
        <?php foreach ($footerScripts as $script): ?>
            <script src="<?php echo baseUrl($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>