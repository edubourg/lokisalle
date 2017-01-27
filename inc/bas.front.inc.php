       <!-- Footer -->
	   <footer>
            <div class="row">
                <div class="col-lg-12">
			<a href="#">@ Eric Dubourg 2016 Tous droits réservés</a>
			<a href="<?php echo RACINE_SITE;?>mentions_legales.php">Mentions légales</a>
			<a href="<?php echo RACINE_SITE;?>cgv.php">C.G.V</a>
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->

	<script type="text/javascript" src="//code.jquery.com/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	
	<!-- Javascript pour fenêtres modales -->
	<script   src="https://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>
	<script src="<?php echo RACINE_SITE;?>js/jquery.modal.js" type="text/javascript" charset="utf-8"></script>

	<!-- Script -->
 	<!-- Javascript pour fenêtres date -->
	<script type="text/javascript" src="<?php echo RACINE_SITE;?>js/datepickr.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
	<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

      <script type="text/javascript">
            $(function () {
                $('#date_arrivee').datetimepicker({
                    locale: 'fr'
                });
            });
        </script>
      <script type="text/javascript">
            $(function () {
                $('#date_depart').datetimepicker({
                    locale: 'fr'
                });
            });
        </script>

	
</body>

</html>
