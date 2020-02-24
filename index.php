
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


// POOL SETTINGS
// CUSTOMIZE THIS SECTION
$poolFees 			= "0";
$poolTax 			= "2";
$poolID				= "bd190d24622cf29094149258431fe5f8d06b810e649325c176356dbf95970422";
$networkType		= "Incentivized Testnet";
$poolTax			= "2%";
$poolFees			= "0";
$poolTicker			= "COCO";
$networkLastHash 	= "#";
$siteTitle			= "COCONUT POOL";
$siteSlogan			= "A Cardano ADA Stake Pool";
$apiPort			= "5000";
$fqdn				= "coconutpool.com";

// SHELLEY EXPLORER
$shelley = "https://shelleyexplorer.cardano.org/en/block";

// CREATED BY JORMANAGER
// SET PATH, DO NOT START WITH A "/" 
$jmblocks			= "blocks.json";
$jmstats 			= "jormanager-stats.json";

// POOL DATA: BLOCKS
// SOURCE: JORMANAGER
$finit 	= curl_init();
$furl 	= "https://" . $fqdn . "/" . $jmblocks;
curl_setopt($finit, CURLOPT_URL, $furl);
curl_setopt($finit, CURLOPT_RETURNTRANSFER, true);
$blockSource  = curl_exec($finit);
curl_close($finit);

$blockJSON		= json_decode($blockSource, true);

// NODE STATUS
// SOURCE: JORMANAGER

$fsinit 	= curl_init();
$fsurl 	= "https://" . $fqdn . "/" . $jmstats;
curl_setopt($fsinit, CURLOPT_URL, $fsurl);
curl_setopt($fsinit, CURLOPT_RETURNTRANSFER, true);
$nodeSource  = curl_exec($fsinit);
curl_close($fsinit);

$nodeJSON		= json_decode($nodeSource, true);

// LEADER SLOTS TODAY
foreach ($blockJSON as $blockData) {
 	if ($blockData['status'] == 'Pending') {
 		$l++;
 	}
}

$leadersToday	= $l;


// POOL DATA: STAKE
// SOURCE: POOLTOOL.IO OPERATORS DATA
$lsinit 	= curl_init();
$lsurl 	= "https://pooltool.s3-us-west-2.amazonaws.com/8e4d2a3/pools/". $poolID . "/livestats.json";
curl_setopt($lsinit, CURLOPT_URL, $lsurl);
curl_setopt($lsinit, CURLOPT_RETURNTRANSFER, true);
$liveSource = curl_exec($lsinit);
curl_close($lsinit);

$liveJSON		= json_decode($liveSource, true);

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

// POOL DATA: STATISTICS
$liveStake 		= number_format(round($liveJSON['livestake']*0.000001));
$lifetimeBlocks	= $liveJSON['lifetimeblocks'];

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
$currentEpoch 	= round($localStatsJSON['lastBlockDate']);
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

$totalRewards 	= round(($valueForStakers*0.000001)*0.3);
$totalTaxes 	= round(($valueTaxed*0.000001)*0.3);   

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
	    		<div class="col s12 m12 orangebg" style="padding: 1em;">
	    			<div class="col s12 m6 headline">
	    				<h1 class="headline"><?php echo $siteTitle; ?></h1>
	    			</div>
	    			<div class="col s4 m2 content eightteen">
	    				<a class="modal-trigger" href="#about">ABOUT</a>
	    			</div>
		   			<div class="col s4 m2 content eightteen">
		   				<a class="modal-trigger" href="#contact">CONTACT</a>
		   			</div>
	    			<div class="col s4 m2 content eightteen">
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
				<div id="about" class="modal">
				    <div class="modal-content">
				      	<h4 class="headline">ABOUT</h4>
				      	<p> ... </p>
				    </div>
				    <div class="modal-footer">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
				
				<!-- MODAL: CONTACT -->
				<div id="contact" class="modal">
				    <div class="modal-content">
				      	<h4 class="headline">CONTACT</h4>
				      	<p> ... </p>
				    </div>
				    <div class="modal-footer">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
				
				<!-- MODAL: HARDWARE -->
				 <div id="hardware" class="modal">
				    <div class="modal-content">
				      	<h4 class="headline">HARDWARE</h4>
				      <p> ... </p>
				    </div>
				    <div class="modal-footer">
				      <a href="#!" class="modal-close btn-flat">CLOSE</a>
				    </div>
				</div>
    		</header>

			<main>
			
			<!-- MAIN CONTENT -->
			<div class="col s12 m12 dijonbg" style="padding-bottom: 1em;">
				<!-- EPOCH -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">EPOCH</h2>

						<!-- ITEM -->
						<div class="col s6 m6 headline">CURRENT EPOCH</div>
						<div class="col s6 m6 content">
							<?php echo $currentEpoch; ?>
						</div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">CURRENT SLOT</div>
						<div class="col s6 m6 content">
							<?php echo $currentSlot; ?>
						</div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">TOTAL SLOTS</div>
						<div class="col s6 m6 content">
							<?php echo $totalSlots; ?>
						</div>


						<!-- ITEM -->
						<div class="col s6 m6 headline">PERCENT COMPLETE</div>
						<div class="col s6 m6 content">
							<?php echo $currentProg; ?>%
						</div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">TIME TO NEXT EPOCH</div>
						<div class="col s6 m6"><?php echo $nextEpoch; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">&nbsp;</div>
						<div class="col s6 m6">&nbsp;</div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">&nbsp;</div>
						<div class="col s6 m6">&nbsp;</div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">&nbsp;</div>
						<div class="col s6 m6">&nbsp;</div>

					</div>
				</article>

				<!-- POOL -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">POOL</h2>

						<!-- ITEM -->
						<div class="col s6 m6 headline">TICKER</div>
						<div class="col s6 m6"><?php echo $poolTicker; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">TAX</div>
						<div class="col s6 m6"><?php echo $poolTax; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">FEES</div>
						<div class="col s6 m6">₳ <?php echo $poolFees; ?></div>
						
						<!-- ITEM -->
						<div class="col s6 m6 headline">LIVE STAKE</div>
						<div class="col s6 m6 content">₳ <?php echo $liveStake; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">DELEGATORS</div>
						<div class="col s6 m6 content"><?php echo $totalDelegators; ?></div>


						<!-- ITEM -->
						<div class="col s6 m6 headline">TOTAL REWARDS</div>
						<div class="col s6 m6 content">₳ <?php echo $totalRewards; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">TOTAL TAXES</div>
						<div class="col s6 m6 content">₳ <?php echo $totalTaxes; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">LEADERSHIP TODAY</div>
						<div class="col s6 m6 content"><?php echo $leadersToday; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">LEADERSHIP TOTAL</div>
						<div class="col s6 m6 content"><?php echo $lifetimeBlocks; ?></div>

					</div>
				</article>

				<!-- NETWORK -->
				<article>
					<div class="col s12 m4">
						<h2 class="headline">NETWORK</h2>

						<!-- ITEM -->
						<div class="col s6 m6 headline">NETWORK</div>
						<div class="col s6 m6 content"><?php echo $networkType; ?></div>


						<!-- ITEM -->
						<div class="col s6 m6 headline">CLIENT</div>
						<div class="col s6 m6 content"><?php echo $networkClient; ?></div>
						
						<!-- ITEM -->
						<div class="col s6 m6 headline">NETWORK CHAIN HEIGHT</div>
						<div class="col s6 m6 content"><?php echo $chainHeight; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">NETWORK LAST HASH</div>
						<div class="col s6 m6 content"><?php echo $networkLastHash; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">LOCAL CHAIN HEIGHT</div>
						<div class="col s6 m6 content"><?php echo $localBlockHeight; ?></div>

						<!-- ITEM -->
						<div class="col s6 m6 headline">LOCAL LAST HASH</div>
						<div class="col s6 m6"><a href='<?php echo $shelley . "/" . $localLastHash; ?>'><?php echo substr($localLastHash, 0,16); ?></a></div>

						

					</div>
				</article>
			</div>


			<!-- NODE STATUS LEGEND -->
			<div class="hide-on-med-and-up">
					
					<div class="col s12 lightyellowbg" style="padding-top: 1em; padding-bottom: 1em">
						<!-- NODE ID -->
						<div class="col s3">
							
								<div class="col s12 square pumpkin headline">JMGR</div>
							
						</div>

						<!-- STATE -->
						<div class="col s3 m2">
							<div class="col s6 square running headline">RN</div>
							<div class="col s6 square bootstrap headline">BS</div>
						</div>

						<!-- LEADERSHIP -->
						<div class="col s3 m2">
							<div class="col s6 square leadership headline">LD</div>
							<div class="col s6 square headline" style="background: rgba(34, 112, 147, .5);">PA</div>
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col s3">
							
							<div class="col s12 square graybg headline">HT</div>
						
						</div>

					</div>
			</div>	

			<div class="hide-on-small-only">
					
					<div class="col m12 lightyellowbg" style="padding-top: 1em; padding-bottom: 1em">
						<!-- NODE ID -->
						<div class="col m2">
							
								<div class="col m12 square pumpkin headline">JORMUNGANDR</div>
							
						</div>

						<!-- STATE -->
						<div class="col m2">
							<div class="col m6 square running headline">RUNNING</div>
							<div class="col m6 square bootstrap headline">BOOTSTRAP</div>
						</div>

						<!-- LEADERSHIP -->
						<div class="col m2">
							<div class="col m6 square leadership headline">LEADER</div>
							<div class="col m6 square headline" style="background: rgba(34, 112, 147, .5);">PASSIVE</div>
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col m2">
							
							<div class="col m12 square graybg headline">HEIGHT</div>
						
						</div>

						<!-- PEERS -->
						<div class="col m2">
							
							<div class="col m12 square graybg headline">PEERS</div>
							
						</div>

						<!-- UPTIME -->
						<div class="col m2">
							
							<div class="col m12 square graybg headline">UPTIME</div>
							
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
							
								<div class="col s12 m12 square pumpkin headline"><?php echo $n; ?></div>
							
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
									echo "<div class='col s6 m6 square'>&nbsp;</div><div class='col s6 m6 square leadership' style='opacity: 0.5;'>&nbsp;</div>";
								}
								?>
							
						</div>

						<!-- CHAIN HEIGHT -->
						<div class="col s3 m2">
							
							<div class="col s12 m12 square graybg"><?php echo $nodes['lastBlockHeight']; ?></div>
						
						</div>

						<!-- PEERS -->
						<div class="col m2 hide-on-small-only">
							
							<div class="col m12 square graybg"><?php echo $nodes['numberOfPeers']; ?></div>
							
						</div>

						<!-- UPTIME -->
						<div class="col m2 hide-on-small-only">
							
							<div class="col m12 square graybg"><?php echo $nodes['uptime']; ?></div>
							
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
				<div class="col s12 m12 graybg">
					
					<p class="content"><strong>Cardano is an open-source project.</strong> Cardano is a software platform ONLY and does not conduct any independent diligence on, or substantive review of, any blockchain asset, digital currency, cryptocurrency or associated funds. You are fully and solely responsible for evaluating your investments, for determining whether you will exchange blockchain assets based on your own judgment, and for all your decisions as to whether to exchange blockchain assets with Cardano. In many cases, blockchain assets you exchange on the basis of your research may not increase in value, and may decrease in value. Similarly, blockchain assets you exchange on the basis of your research may fall or rise in value after your exchange. <strong>Past performance is not indicative of future results. Any investment in blockchain assets involves the risk of loss of part or all of your investment. The value of the blockchain assets you exchange is subject to market and other investment risks.</strong>
					</p>

				</div>

				<!-- COPYRIGHT -->
				<div class="col s12 m12 darkgraybg">
					<p class="headline center-align">&copy; 2020 <?php echo $siteTitle; ?> &middot; Design by <a href="http://instagram.com/jon_made_this" target="_blank">JON_MADE_THIS</a></p>
				</div>


			</footer>  


    	</div>		
	   
    </body>
</html>
