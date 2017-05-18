			
			<!-- Footer -->
			<?php echo ($page == '100' or $page == '101' or $page == '102' ) ? $backfooterDispley : '';?> 
		</div>
		
		<script src="back_main.js"></script>
        <script src="scrollfix.js" type="text/javascript"></script>
        <script type="text/javascript">
			function clickit(){
				document.getElementById('closeit').click();
			}
			<?php echo($print); ?> 
		</script>  
		
	</body>
</html>