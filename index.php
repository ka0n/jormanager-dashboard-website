
<?php 
/////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////
//
// A GENERIC TEMPLATE FOR A STAKE POOL WEBSITE THAT USES JORMANAGER
// 
// REQUIREMENTS: PHP
// REQUIREMENTS: JORMANAGER (https://bitbucket.org/muamw10/jormanager/)
//
//


// COUNTERS
// DO NOT CHANGE
$d = 0; // deletegators
$e = 0; // epochs
$l = 0; // leaders
$n = 1; // nodes
$f = 0; // false minted blocks

// POOL SETTINGS
// CUSTOMIZE THIS SECTIOn
$poolFees 		= "0";
$poolTax 		= "2";
$poolID			= "bd190d24622cf29094149258431fe5f8d06b810e649325c176356dbf95970422";
$networkType		= "SHELLEY ITN";
$poolTax		= "2%";
$poolFees		= "0";
$poolTicker		= "COCO";

// CONTACT INFO
$poolTwitter		= "COCONUT_POOL";
$poolTelegram		= "COCONUT_POOL";
$poolTelegramChan	= "COCONUTPOOLSTAKERSLOUNGE";
 
// POOL NAME
$siteTitle			= "COCONUT POOL";
$siteSlogan			= "A Cardano ADA Stake Pool";

// API PORT
$apiPort			= "5000";

// YOUR DOMAIN NAME
$fqdn				= "coconutpool.com";

// CREATED BY JORMANAGER
// SET PATH, DO NOT START WITH A "/" 
$jmblocks			= "blocks.json";
$jmstats 			= "jormanager-stats.json";

// SERVER HARDWARE 
$serverType			= "DEDICATED"; // dedicated, vps, vm, etc.
$serverOS			= "UBUNTU 18.04.4"; // operating system
$serverCPU			= "XEON E3-1270v6"; // cpu type
$serverMem			= "32GB DDR4"; // ram
$serverBand			= "500Mbps UNMETERED UP/DOWN"; // network bandwidth
$serverHD			= "2x450GB SSD NVMe SoftRAID"; // disk space


///////// END CUSTOMIZE /////////////////


// NOT AVAILABLE AT THE CURRENT TIME
$networkLastHash 	= "#";


// SHELLEY EXPLORER
$shelley = "https://shelleyexplorer.cardano.org/en/block";

		



// NODE STATUS
// SOURCE: JORMANAGER

$fsinit 	= curl_init();
$fsurl 	= "https://" . $fqdn . "/" . $jmstats;
curl_setopt($fsinit, CURLOPT_URL, $fsurl);
curl_setopt($fsinit, CURLOPT_RETURNTRANSFER, true);
$nodeSource  = curl_exec($fsinit);
curl_close($fsinit);

$nodeJSON		= json_decode($nodeSource, true);


// POOL DATA: STAKE
// SOURCE: POOLTOOL.IO OPERATORS DATA
$lsinit 	= curl_init();
$lsurl 	= "https://pooltool.s3-us-west-2.amazonaws.com/8e4d2a3/pools/". $poolID . "/livestats.json";
curl_setopt($lsinit, CURLOPT_URL, $lsurl);
curl_setopt($lsinit, CURLOPT_RETURNTRANSFER, true);
$liveSource = curl_exec($lsinit);
curl_close($lsinit);

$liveJSON		= json_decode($liveSource, true);
$liveStake 		= number_format(round($liveJSON['livestake']*0.000001));
$lifetimeBlocks	= $liveJSON['lifetimeblocks'];


// POOL DATA: BLOCKS
// SOURCE: JORMANAGER
$finit 	= curl_init();
$furl 	= "https://" . $fqdn . "/" . $jmblocks;
curl_setopt($finit, CURLOPT_URL, $furl);
curl_setopt($finit, CURLOPT_RETURNTRANSFER, true);
$blockSource  = curl_exec($finit);
curl_close($finit);

$blockJSON		= json_decode($blockSource, true);

// LEADER SLOTS TODAY
foreach ($blockJSON as $blockData) {
 	if ($blockData['status'] == 'Pending') {
 		$l++;
 	}
 	if (!$blockData['minted'] == 'false') {
 		$f++;
 		$ff = $f-$l;
 	}
}

$leadersFail		= $ff;
$leadersToday		= $l;
$leadersTotalTotal	= $lifetimeBlocks+$leadersFail;

// POOL DATA: DELEGATORS
// SOURCE: POOLTOOL.IO OPERATORS DATA
$dinit 	= curl_init();
$durl 	= "https://pooltool.s3-us-west-2.amazonaws.com/8e4d2a3/pools/" . $poolID . "/delegators.json";
curl_setopt($dinit, CURLOPT_URL, $durl);
curl_setopt($dinit, CURLOPT_RETURNTRANSFER, true);
$delegateSource = curl_exec($dinit);
curl_close($dinit);

$delegateJSON	= json_decode($delegateSource, true);

foreach ($delegateJSON['d'] as $delegator) {
	$d++;
}

$totalDelegators = $d;



// POOL DATA: LOCAL STATS
// SOURCE: LOCAL NODE
$linit 	= curl_init();
$lurl 	= "http://127.0.0.1:" . $apiPort . "/api/v0/node/stats";
curl_setopt($linit, CURLOPT_URL, $lurl);
curl_setopt($linit, CURLOPT_RETURNTRANSFER, true);
$localStats = curl_exec($linit);
curl_close($linit);

$localStatsJSON		= json_decode($localStats, true);
$localBlockHeight	= $localStatsJSON['lastBlockHeight'];
$localLastHash		= $localStatsJSON['lastBlockHash'];
$networkClient		= substr($localStatsJSON['version'],12);

// NETWORK DATA: CHAIN HEIGHT
// SOURCE: POOLTOOL.IO OPERATORS DATA
$pinit 	= curl_init();
$purl 	= "https://pooltool.s3-us-west-2.amazonaws.com/stats/stats.json";
curl_setopt($pinit, CURLOPT_URL, $purl);
curl_setopt($pinit, CURLOPT_RETURNTRANSFER, true);
$pooltoolSource = curl_exec($pinit);
curl_close($pinit);

$pooltoolJSON	= json_decode($pooltoolSource, true);
$chainHeight 	= $pooltoolJSON['majoritymax'];


// DATE 
if (substr($localStatsJSON['lastBlockDate'], 0,2) <= "99") {
	$currentEpoch 	= substr($localStatsJSON['lastBlockDate'], 0,2);
}
else {
	$currentEpoch 	= substr($localStatsJSON['lastBlockDate'], 0,3);
}

$currentSlot  	= substr($localStatsJSON['lastBlockDate'], 3);
$totalSlots		= "43200";
$currentProg	= round(($currentSlot/$totalSlots)*100);

// TOTAL REWARDS
// TOTAL TAXES COLLECTED
// SOURCE: POOLTOOL.IO OPERATORS DATA

for ($e = 0; $e <= $currentEpoch; $e++) {

	$rinit 	= curl_init();
	$rurl 	= "https://pooltool.s3-us-west-2.amazonaws.com/8e4d2a3/pools/". $poolID . "/rewards_" . $e . ".json";
	curl_setopt($rinit, CURLOPT_URL, $rurl);
	curl_setopt($rinit, CURLOPT_RETURNTRANSFER, true);
	$rewardsSource = curl_exec($rinit);
	curl_close($rinit);
	
	$rewardsJSON   		= json_decode($rewardsSource, true);
    $valueForStakers 	+= $rewardsJSON['rewards']['value_for_stakers'];
    $valueTaxed 		+= $rewardsJSON['rewards']['value_taxed'];
}

$totalRewards 	= number_format(round(($valueForStakers*0.000001)*0.3));
$totalTaxes 	= number_format(round(($valueTaxed*0.000001)*0.3));   

// EPOCH COUNTDOWN
strtotime('today 19:13:37');
date_default_timezone_set('Etc/UCT');
$today = strtotime('today 19:13:37');
$tomorrow = strtotime('tomorrow 19:13:37');
$now = time();
$timeLeft = ($now > $today ? $tomorrow : $today) - $now;
$nextEpoch = gmdate("H:i:s", $timeLeft);



?>
<!-- MAIN DOCUMENT HTML SOURCE -->
<!DOCTYPE html>
<html>
    <head>

	    <!-- CSS: MATERIALIZE -->
	    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
  		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	    <!-- CSS: GOOGLE FONTS -->
	    <link href="https://fonts.googleapis.com/css?family=Itim|Francois+One|News+Cycle&display=swap" rel="stylesheet">

	    <!-- CSS: COLORS & FONTS -->
	    <link href="css/style.css" rel="stylesheet">


		<!-- JS: JQUERY 3.4.1 -->
	    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>   
	    

	    <!-- JS: MATERIALIZE -->
	    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
	    
	    
	    <!-- VIEWPORT -->
	    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	    <!-- TITLE -->
	    <title><?php echo $siteTitle; ?> | <?php echo $siteSlogan; ?></title>
    </head>

    <body class="whitebg">

       	<div class="row">
       		<!-- TOPO BAR: LOGO & MENU -->
       		<header>
	    		<div class="col s12 m12 header" style="padding: 1em;">
	    			<div class="col s12 m6">
	    				<h1 class="logo"><?php echo $siteTitle; ?></h1>
	    			</div>
	    			<div class="col s4 m2 content">
	    				<a class="modal-trigger" href="#about">ABOUT</a>
	    			</div>
		   			<div class="col s4 m2 content">
		   				<a class="modal-trigger" href="#contact">CONTACT</a>
		   			</div>
	    			<div class="col s4 m2 content">
	    				<a class="modal-trigger" href="#hardware">HARDWARE</a>
	    			</div>
	    		</div>
	    		<!-- TOP BAR: MODAL WINDWS -->
		    	<!-- MODAL INITIALIZE -->
		    	<script type="text/javascript">
		    		$(document).ready(function(){
					    $('.modal').modal();
					});
		    	</script>
	    		<!-- MODAL: ABOUT -->
				<div id="about" class="modal modalbg">
				    <div class="modal-content">
				      	<h4 class="title">ABOUT</h4>
				      	<p class="content">Coconut Pool is a Cardano ADA staking pool.</p>
				    </div>
				    <div class="modalfooter">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
				
				<!-- MODAL: CONTACT -->
				<div id="contact" class="modal modalbg">
				    <div class="modal-content">
				      	<h4 class="title">CONTACT</h4>
				      	<table class="">
				      		<tr>
				      			<td width="15%"><p class="title">Twitter:</p></td>
				      			<td width="85%"><p class="content"><a href="http://twitter.com/<?php echo $poolTwitter; ?>" target="_blank">@<?php echo $poolTwitter; ?></a></p></td>
				      		</tr>
				      		<tr>
				      			<td width="15%"><p class="title">Telegram:</p></td>
				      			<td width="85%"><p class="content"><a href="http://t.me/<?php echo $poolTelegramChan; ?>" target="_blank">@<?php echo $poolTelegramChan; ?></a></p></td>
				      		</tr>
				      	</table>
				    </div>
				    <div class="modalfooter">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
				
				<!-- MODAL: HARDWARE -->
				 <div id="hardware" class="modal modalbg">
				    <div class="modal-content">
				      	<h4 class="title">HARDWARE</h4>
				      	<table class="">
			      		<tr>
			      			<td class="title">TYPE:</td>
			      			<td class="content"><?php echo $serverType; ?></td>
			      		</tr>
			      		<tr>
			      			<td class="title">SYSTEM:</td>
			      			<td class="content"><?php echo $serverOS; ?></td>
			      		</tr>
			      		<tr>
			      			<td class="title">PROCESSOR:</td>
			      			<td class="content"><?php echo $serverCPU; ?></td>
			      		</tr>
			      		<tr>
			      			<td class="title">MEMORY:</td>
			      			<td class="content"><?php echo $serverMem; ?></td>
			      		</tr>
			      		<tr>
			      			<td class="title">DISK:</td>
			      			<td class="content"><?php echo $serverHD; ?></td>
			      		</tr>
			      		<tr>
			      			<td class="title">BANDWIDTH:</td>
			      			<td class="content"><?php echo $serverBand; ?></td>
			      		</tr>

			      		</table>
				    </div>
				    <div class="modalfooter">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
    		</header>

			<main>
			
			<!-- MAIN CONTENT -->
			<div class="col s12 m12 statistics" style="padding-bottom: 1em;">
				<!-- EPOCH -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">EPOCH</h2>

						<!-- ITEM 1-->
						<div class="col s6 m6 title">CURRENT EPOCH</div>
						<div class="col s6 m6 content">
							<?php

							 if ( $currentEpoch == "") {
							 	echo "Bootstrapping..";
							 }
							 else {
							 	echo $currentEpoch; 
							 }

							 ?>
						</div>

						<!-- ITEM 2-->
						<div class="col s6 m6 title">CURRENT SLOT</div>
						<div class="col s6 m6 content">
							<?php

							 if ( $currentSlot == "") {
							 	echo "Bootstrapping..";
							 }
							 else {
							 	echo $currentSlot;
							 }

							 ?>
							
						</div>

						<!-- ITEM 3-->
						<div class="col s6 m6 title">TOTAL SLOTS</div>
						<div class="col s6 m6 content">
							<?php echo $totalSlots; ?>
						</div>


						<!-- ITEM 4-->
						<div class="col s6 m6 title">PERCENT COMPLETE</div>
						<div class="col s6 m6 content">
							<?php

							 if ( $currentProg == "") {
							 	echo "Bootstrapping..";
							 }
							 else {
							 	echo $currentProg;
							 }

							 ?>
							
						</div>


						<!-- ITEM 5-->
						<div class="col s6 m6 title">LEADERS LEFT TODAY</div>
						<div class="col s6 m6 content"><?php echo $leadersToday; ?></div>

						<!-- ITEM 5-->
						<div class="col s6 m6 title">LIFETIME LEADERSHIP</div>
						<div class="col s6 m6 content"><?php echo $lifetimeBlocks; ?></div>

						<!-- ITEM 7-->
						<div class="col s6 m6 title">TIME TO NEXT EPOCH</div>
						<div class="col s6 m6 content"><?php echo $nextEpoch; ?></div>

					

						
					</div>
				</article>

				<!-- POOL -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">POOL</h2>

						<!-- ITEM 1-->
						<div class="col s6 m6 title">TICKER</div>
						<div class="col s6 m6 content"><?php echo $poolTicker; ?></div>

						<!-- ITEM 2-->
						<div class="col s6 m6 title">TAX</div>
						<div class="col s6 m6 content"><?php echo $poolTax; ?></div>

						<!-- ITEM 3-->
						<div class="col s6 m6 title">FEES</div>
						<div class="col s6 m6 content">₳ <?php echo $poolFees; ?></div>
						
						<!-- ITEM 4-->
						<div class="col s6 m6 title">LIVE STAKE</div>
						<div class="col s6 m6 content">
							<?php
								if ($liveStake == "") {
									echo "Bootstrapping..";
								}
								else {
									echo '₳ ' . $liveStake; 
								}
							?> 
						</div>

						<!-- ITEM 5-->
						<div class="col s6 m6 title">DELEGATORS</div>
						<div class="col s6 m6 content">
							<?php
								if ($totalDelegators == "") {
									echo "Bootstrapping..";
								}
								else {
									echo $totalDelegators; 
								}
							?> 
						</div>


						<!-- ITEM 6-->
						<div class="col s6 m6 title">TOTAL REWARDS</div>
						<div class="col s6 m6 content">
							<?php
								if ($totalRewards == "0") {
									echo "Bootstrapping..";
								}
								else {
									echo '₳ ' . $totalRewards; 
								}
							?> 
						</div>

						<!-- ITEM 7-->
						<div class="col s6 m6 title">TOTAL TAXES</div>
						<div class="col s6 m6 content">
								<?php
								if ($totalTaxes == "0") {
									echo "Bootstrapping..";
								}
								else {
									echo '₳ ' . $totalTaxes; 
								}
							?> 
						</div>


					</div>
				</article>

				<!-- NETWORK -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">NETWORK</h2>

						<!-- ITEM 1-->
						<div class="col s6 m6 title">NETWORK</div>
						<div class="col s6 m6 content"><?php echo $networkType; ?></div>


						<!-- ITEM 2-->
						<div class="col s6 m6 title">CLIENT</div>
						<div class="col s6 m6 content"><?php echo $networkClient; ?></div>
						
						<!-- ITEM 3-->
						<div class="col s6 m6 title">NETWORK CHAIN</div>
						<div class="col s6 m6 content"><?php echo $chainHeight; ?></div>

						<!-- ITEM 4-->
						<div class="col s6 m6 title">NETWORK HASH</div>
						<div class="col s6 m6 content"><?php echo $networkLastHash; ?></div>

						<!-- ITEM 5-->
						<div class="col s6 m6 title">LOCAL CHAIN</div>
						<div class="col s6 m6 content">
							<?php 

								if ($localBlockHeight == "") {
									echo "Bootstrapping.."; 
								}
								else {
									echo $localBlockHeight; 
								}
							?>
						</div>

						<!-- ITEM 6-->
						<div class="col s6 m6 title">LOCAL HASH</div>
						<div class="col s6 m6 content">
							<?php
								if ($localLastHash == "") {
									echo "Bootstrapping..";
								}
								else {
									echo '<a href=' . $shelley . "/" . $localLastHash . ' target="_blank">' . substr($localLastHash, 0,16) . '</a>'; 
								}
							?>
						</div>

						<!-- ITEM 7-->
						<div class="col s6 m6 title">&nbsp;</div>
						<div class="col s6 m6">&nbsp;</div>
						
				

					

					</div>
				</article>
			</div>


			<!-- NODE STATUS LEGEND -->
			<div class="hide-on-med-and-up">
					
					<div class="col s12 legend" style="padding-top: 1em; padding-bottom: 1em">
						<!-- NODE ID -->
						<div class="col s3">
							
								<div class="col s12 square node title">NODE</div>
							
						</div>

						<!-- STATE -->
						<div class="col s3 m2">
							<div class="col s6 square running title">RN</div>
							<div class="col s6 square bootstrap title">BS</div>
						</div>

						<!-- LEADERSHIP -->
						<div class="col s3 m2">
							<div class="col s6 square leadership title">LD</div>
							<div class="col s6 square title passive">PA</div>
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col s3">
							
							<div class="col s12 square block title">HT</div>
						
						</div>

					</div>
			</div>	

			<div class="hide-on-small-only">
					
					<div class="col m12 legend" style="padding-top: 1em; padding-bottom: 1em">
						<!-- NODE ID -->
						<div class="col m2">
							
								<div class="col m12 square node title">JORMUNGANDR</div>
							
						</div>

						<!-- STATE -->
						<div class="col m2">
							<div class="col m6 square running title">RUNNING</div>
							<div class="col m6 square bootstrap title">BOOTSTRAP</div>
						</div>

						<!-- LEADERSHIP -->
						<div class="col m2">
							<div class="col m6 square leadership title">LEADER</div>
							<div class="col m6 square title passive">PASSIVE</div>
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col m2">
							
							<div class="col m12 square block title">HEIGHT</div>
						
						</div>

						<!-- PEERS -->
						<div class="col m2">
							
							<div class="col m12 square block title">PEERS</div>
							
						</div>

						<!-- UPTIME -->
						<div class="col m2">
							
							<div class="col m12 square block title">UPTIME</div>
							
						</div>
					</div>
			</div>	

			<!-- NODE STATUS --->
	
			<div class="">
								
					<?php

					foreach ($nodeJSON['nodes'] as $nodes) {
							
					?>

					<div class="col s12 m12" style="padding-top: 0.5em; padding-bottom: 0.5em">
						<!-- NODE ID -->
						<div class="col s3 m2">
							
								<div class="col s12 m12 square node title content"><?php echo $n; ?></div>
							
						</div>

						<!-- STATE -->
						<div class="col s3 m2">
							
								<?php
								if ($nodes['state'] == 'Running') {
									echo '<div class="col s6 m6 square running">&nbsp;</div><div class="col s6 m6 square">&nbsp;</div>'; 
								}

								if ($nodes['state'] == 'Bootstrapping') {
									echo '<div class="col s6 m6 square">&nbsp;</div><div class="col s6 m6 square bootstrap">&nbsp;</div>';
								}
								?>
							
						</div>

						<!-- LEADERSHIP -->
						<div class="col s3 m2">
							
								<?php

								if ($nodes['leader'] == 'true') {
									echo "<div class='col s6 m6 square leadership'>&nbsp;</div><div class='col s6 m6 square'>&nbsp;</div>";
								}
								else {
									echo "<div class='col s6 m6 square'>&nbsp;</div><div class='col s6 m6 square passive'>&nbsp;</div>";
								}
								?>
							
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col s3 m2">
							
							<div class="col s12 m12 square block">
								<a href="<?php echo $shelley . '/' . $nodes['lastBlockHash']; ?>" target="_blank"><?php echo $nodes['lastBlockHeight']; ?></a>
							</div>
						
						</div>

						<!-- PEERS -->
						<div class="col m2 hide-on-small-only">
							
							<div class="col m12 square block content"><?php echo $nodes['numberOfPeers']; ?></div>
							
						</div>

						<!-- UPTIME -->
						<div class="col m2 hide-on-small-only">
							
							<div class="col m12 square block content"><?php echo $nodes['uptime']; ?></div>
							
						</div>
					</div>

					<?php
							// INCREASE NODE COUNTER
							$n++;
						}
						
					?>
			</div>	

				
			</main>

			<footer>
				<!-- DISCLAIMER -->
				<div class="col s12 m12 disclaimer">
					
					<p class="content"><strong>Cardano is an open-source project.</strong> Cardano is a software platform ONLY and does not conduct any independent diligence on, or substantive review of, any blockchain asset, digital currency, cryptocurrency or associated funds. You are fully and solely responsible for evaluating your investments, for determining whether you will exchange blockchain assets based on your own judgment, and for all your decisions as to whether to exchange blockchain assets with Cardano. In many cases, blockchain assets you exchange on the basis of your research may not increase in value, and may decrease in value. Similarly, blockchain assets you exchange on the basis of your research may fall or rise in value after your exchange. <strong>Past performance is not indicative of future results. Any investment in blockchain assets involves the risk of loss of part or all of your investment. The value of the blockchain assets you exchange is subject to market and other investment risks.</strong>
					</p>

				</div>

				<!-- COPYRIGHT -->
				<div class="col s12 m12 footer">
					<p class="title center-align">&copy; 2020 <a href="https://github.com/coconutpool/jormanager-dashboard-website" tagret="_blank">COCONUT POOL</a> &middot; Design by <a href="http://instagram.com/jon_made_this" target="_blank">JON_MADE_THIS</a></p>
				</div>


			</footer>  


    	</div>		
	   
    </body>
</html>
