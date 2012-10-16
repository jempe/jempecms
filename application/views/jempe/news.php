
		<div class="item">
<?php
setlocale(LC_TIME, $this->lang->line('jempe_locale')  );

$time = explode("-",substr($timestamp,0,10) );

$date = mktime(0,0,0,$time[1],$time[2],$time[0]);


 ?>
			<div class="date">

				<div><?= strftime("%b", $date) ?></div>
				<span><?= strftime("%d", $date) ?></span>
			</div>

			<div class="content">

				<h1><?= $title ?></h1>

				<div class="body">

					<?= $text ?>

				</div>

			</div>

		</div>
		
		
