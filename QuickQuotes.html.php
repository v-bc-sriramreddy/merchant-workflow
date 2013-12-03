<!DOCTYPE html>
<html>
	<head>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="css/bootstrap.css">
		<style>
			#mainWrapper
			{
				padding-left:20px;
			}
			.text-error
			{
				font-size: 12px !important;
				min-height: 20px !important;
				margin-bottom:4px;
			}

			select
			{
				width:330px;
			}

			.shortInputs
			{
				width:150px;
			}
			
			label
			{
				padding-top:5px;
			}
			#resultsDiv
			{
				padding-top:20px;
			}
			#inputDiv
			{
				padding-top:20px;
			}
			#ExceptionsDiv
			{
				padding-top:20px;
			}

			.manadatoryMark
			{
				font-size:16px !important;
				padding-left:20px;
			}
			#inputTables{
				min-width:90% !important;
				max-width:90% !important;
			}
			.changecolor{
				font-weight:bold;
				font-size:20px;
				color:#006DCC;
			}
			.exceptioncolor{
				font-weight:bold;
				font-size:20px;
				color:#006DCC;
			}
		</style>

	</head>
	<body>
		<div class="container">
			<h2 class="text-center">Temando Quick Quotes</h2><br />
			<form action="index.php" method="post" id="quotesForm">
				<div class="span6 row-fluid offset3" id="mainWrapper" >
					<div class="span12">
						<div> </div>
						<div class="span12">
							<span class="pull-left"><label for="originType">Origin type</label></span>
							<label class="span2 radio">
								<input type="radio" name="originIs" class="originType" value="Business" checked="checked" />Business
							</label>
							<label class="span2 radio">
								<input type="radio" name="originIs" class="originType" value="Residence" />Residence
							</label>
							<span class="text-error span4"><?php echo $requestManager->formErrors['originIs'] ; ?></span>							
						</div>
						<div class="span12">
							<span class="span6">
								<input type="text" name="originCode" id="originCode" class="shortInputs"  placeholder="Post code" value="<?php echo $requestManager->regionFields['originCode']; ?>"/>
								<input type="text" name="originSuburb" id="originSuburb" class="shortInputs"  placeholder="Origin suburb" value="<?php echo $requestManager->regionFields['originSuburb']; ?>"/>
							</span>
							<span class="text-error span6"><?php echo $requestManager->formErrors['originCode'] ; ?>&nbsp;&nbsp;&nbsp;<?php echo $requestManager->formErrors['originSuburb'] ; ?></span>
						</div>
						<div class="span12">
							<span class="pull-left">
								<label for="destinationType">Destination type</label>
							</span>
							<span class="span6">
								<label class="span3 radio">
									<input type="radio" name="destinationIs" class="destinationType" value="Business"  checked="checked" />Business
								</label>
								<label class="span3 radio">
									<input type="radio" name="destinationIs" class="destinationType" value="Residence" />Residence
								</label>
							</span>
							<span class="text-error span4"><?php echo $requestManager->formErrors['destinationIs'] ; ?></span>
						</div>
						<div class="span12">							
							<span class="span6">
								<input type="text" name="destinationCode" id="destinationCode" class="shortInputs"  placeholder="Post code" value="<?php echo $requestManager->regionFields['destinationCode']; ?>"/>
								<input type="text" name="destinationSuburb" id="destinationSuburb" class="shortInputs" placeholder="Destination suburb" value="<?php echo $requestManager->regionFields['destinationSuburb']; ?>"/>
							</span>
							<span class="text-error span6"><?php echo $requestManager->formErrors['destinationCode'] ; ?>&nbsp;&nbsp;&nbsp;<?php echo $requestManager->formErrors['destinationSuburb'] ; ?></span>
						</div>
						<div class="span12">
							<span class="span6">
								<select name="packaging" id="packaging">
									<option value="Box">Box</option>
									<option value="Carton">Carton</option>
									<option value="Crate">Crate</option>
									<option value="Cylinder">Cylinder</option>
									<option value="Pallet">Pallet</option>
									<option value="Parcel">Parcel</option>
									<option value="Satchel">Satchel</option>
								</select>								
							</span>
							<span class="text-error span6"><?php echo $requestManager->formErrors['packaging'] ; ?></span>
						</div>
						<div class="span12">							
							<span class="span6">
								<input type="text" name="length" id="length" placeholder="Length(cm)" class="shortInputs" value="<?php echo $requestManager->dimensionFields['length']; ?>"/>
								<input type="text" name="width" id="width" placeholder="Width(cm)" class="shortInputs" value="<?php echo $requestManager->dimensionFields['width']; ?>" />
							</span>
							<span class="text-error span6"><?php echo $requestManager->formErrors['length'] ; ?>&nbsp;&nbsp;&nbsp;<?php echo $requestManager->formErrors['width'] ; ?></span>							
						</div>				
						<div class="span12">							
							<span class="span6">
								<input type="text" name="height" id="height" placeholder="Height(cm)" class="shortInputs" value="<?php echo $requestManager->dimensionFields['height']; ?>"/>
								<input type="text" name="weight" id="weight"  placeholder="Weight(kg)" class="shortInputs" value="<?php echo $requestManager->dimensionFields['weight']; ?>"/>
							</span>	
							<span class="text-error span6"><?php echo $requestManager->formErrors['height'] ; ?>&nbsp;&nbsp;&nbsp;<?php echo $requestManager->formErrors['weight'] ; ?></span>							
						</div>					
					</div>
					
					<div class="span6">
						<span class="text-error manadatoryMark">All fields are mandatory, This prototype expect user to give valid Inputs.</span>
					</div>
					
					<div class="span6 text-center">						
						<button id="cancel" class="btn">Cancel</button>
						<input type="submit" value="Submit" class="btn btn-primary" />				
					</div>
				</div>
			</form>
			
			<?php
				if(isset($regionFields) && isset($dimensionFieldValues))
				{
			?>
			<div class="span12" id="inputDiv">
				<label class="changecolor">Inputs</label>
				<table class="table" id="inputTables">
				<?php
					echo "<tr>";
					echo "<th>origin Type</td>";
					echo "<th>origin Code</td>";
					echo "<th>origin Suburb</td>";
					echo "<th>destination Type</td>";
					echo "<th>destination Code</td>";
					echo "<th>destination Suburb</td>";
					echo "<th>packaging</td>";
					echo "<th>length <sub>(cm)</sub></td>";
					echo "<th>width <sub>(cm)</sub></td>";
					echo "<th>height <sub>(cm)</sub></td>";
					echo "<th>weight <sub>(kg)</sub></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td>".$regionFields['originIs']."</td>";
					echo "<td>".$regionFields['originCode']."</td>";
					echo "<td>".$regionFields['originSuburb']."</td>";
					echo "<td>".$regionFields['destinationIs']."</td>";
					echo "<td>".$regionFields['destinationCode']."</td>";
					echo "<td>".$regionFields['destinationSuburb']."</td>";
					echo "<td>".$dimensionFieldValues['packaging']."</td>";
					echo "<td>".$dimensionFieldValues['length']."</td>";
					echo "<td>".$dimensionFieldValues['width']."</td>";
					echo "<td>".$dimensionFieldValues['height']."</td>";
					echo "<td>".$dimensionFieldValues['weight']."</td>";
					echo "</tr>";
				?>	
				</table>
			</div>				
			<?php
				}
				if ($response['flag'] && (isset($response['quotesList'])))
				{
			?>
			<div class="span12" id="resultsDiv">
				<label class="changecolor">Quotes</label>
				<table class="table" id="resultsTable">
					<tr>
							<th>
								Company name
							</th>
							<th>
								Service
							</th>
							<th>
								ETD
							</th>
							<th>
								AUD
							</th>	
						</tr>
						<?php
							foreach ($response['quotesList'] as $key => $row) 
							{
							echo "<tr>";
								echo "<td>".$row['companyName']."</td>";
								echo "<td>".$row['deliveryMethod']."</td>";
								echo "<td>".$row['$etaFrom']." - ".$row['$etaTo']."</td>";
								echo "<td>".$row['$totalPrice']."</td>";
							echo "</tr>";
							}
						?>	
				</table>
			</div>		
				<?php
					}
					elseif ($response['exceptionMessage'])
						{
							echo "<div  id='ExceptionsDiv' class='span12'>";
							echo "<label class='exceptioncolor'> Temando kicks back</label>";
							echo "<hr/>";
							echo "<label class='text-error manadatoryMark'>" . $response['exceptionMessage'] . "</label>";
							echo "<hr/>";
							echo "</div>";	
						}
				?>
			
		</div>
		<!-- Latest compiled and minified JavaScript -->
		<script type="text/javascript" src="js/jquery.js"></script>
		<script src="js/bootstrap.js"></script>
	</body>
</html>
