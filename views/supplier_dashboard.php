<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard | ProtectBox</title>
    <!-- Favicons-->
    <?php $this->load->view("common/metalinks");?>
    <link href="<?php echo base_url();?>css/date_picker.css" rel="stylesheet">
    <link href="<?php echo base_url();?>css/jquery.switch.css" rel="stylesheet">
	<style>
	.new_div {
		margin-bottom:20px;
	}
	.rounded_div {
		border:1px solid #CCC;
		box-shadow: 0px 0px 3px #bfbfbf;
		border-radius:5px;
	}
	 .main_image
      {
        width:60%;
        margin: 0 15px 30px 0;
      }
	   .zc-ref {
	  display: none;
	}
	</style>
  </head>
  <body>
    <div id="load">
    </div>
    <!-- Mobile menu overlay mask -->
    <!-- Header================================================== -->
    <?php $this->load->view("common/header");?>
    <section id="sub_header" style="background:#f5f5f5;box-shadow:inset 0 1px 10px 1px rgba(0,0,0,.41);">
      <div class="container">
        <div class="main_title" style="background:none;text-align:center;font-size:40px;color:#000;bottom:30px;">
         Dashboard
        </div>
      </div>
    </section>
    <!-- End section -->

    <main>    
      <div class="container margin_60">
        <div class="row">
			<?php $this->load->view("common/supplier_sidebar");?>
			<div class="col-md-9">
				<!--  Tabs -->   
			<!-- <ul class="nav nav-tabs"></ul> -->
			<div class="tab-content rounded_div">
				  <div class=" table-responsive">
					<div class="col-md-12">
						<div class="row" >
							<div class="col-md-4">
								<div id='solution_progress_one'></div>
							</div>
							<div class="col-md-4">
								<div id='solution_progress_two'></div>
							</div>
							<div class="col-md-4">
								<div id='solution_progress_three'></div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div id='graph_solution'></div>
					</div>
				  </div>
				</div>
			  </div><!-- End col-md-12-->
		  </div>
        <!-- End row -->
      </div>
      <!-- End container -->
    </main>
    <!-- End main -->
    <?php $this->load->view("common/footer");?>
    <!-- Specific scripts -->
	<!-- 1st circle progress -->
   <script src= "<?php echo base_url();?>js/zingchart.min.js"></script>
   <script>
		var myConfig = {
		backgroundColor:'#FBFCFE',
			type: "ring",
			title: {
			  text: "Solution 1",
			  fontFamily: 'Lato',
			  fontSize: 14,
			  // border: "1px solid black",
			  fontColor : "#1E5D9E",
			},
		
			plotarea: {
			  backgroundColor: 'transparent',
			  borderWidth: 0,
			  borderRadius: "0 0 0 10",
			  margin: "70 0 10 0"
			},
			legend : {
			toggleAction:'remove',
			backgroundColor:'#FBFCFE',
			borderWidth:0,
			adjustLayout:true,
			align:'center',
			verticalAlign:'bottom',
			marker: {
				type:'circle',
				cursor:'pointer',
				borderWidth:0,
				size:5
			},
			item: {
				fontColor: "#777",
				cursor:'pointer',
				offsetX:-6,
				fontSize:12
			},
			mediaRules:[
				{
					maxWidth:500,
					visible:false
				}
			]
			},
			scaleR:{
			  refAngle:270
			},
			series : [
				{
				  text: "Sold",
					values : [106541],
					lineColor: "#00BAF2",
					backgroundColor: "#00BAF2",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#00BAF2'
					}
				},
				{
				    text: "Unsold",
					values : [56711],
					lineColor: "#E80C60",
					backgroundColor: "#c1bfc1",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#E80C60'
					}
				},
			]
		};
		 
		zingchart.render({ 
			id : 'solution_progress_one', 
		  data: {
			graphset: [myConfig]
		  },
			height: '250', 
			width: '100%' 
		});
   </script>

   <!-- 2nd circle progress -->
    <script>
		var myConfig = {
		backgroundColor:'#FBFCFE',
			type: "ring",
			title: {
			  text: "Solution 2",
			  fontFamily: 'Lato',
			  fontSize: 14,
			  // border: "1px solid black",
			  fontColor : "#1E5D9E",
			},
		
			plotarea: {
			  backgroundColor: 'transparent',
			  borderWidth: 0,
			  borderRadius: "0 0 0 10",
			  margin: "70 0 10 0"
			},
			legend : {
			toggleAction:'remove',
			backgroundColor:'#FBFCFE',
			borderWidth:0,
			adjustLayout:true,
			align:'center',
			verticalAlign:'bottom',
			marker: {
				type:'circle',
				cursor:'pointer',
				borderWidth:0,
				size:5
			},
			item: {
				fontColor: "#777",
				cursor:'pointer',
				offsetX:-6,
				fontSize:12
			},
			mediaRules:[
				{
					maxWidth:500,
					visible:false
				}
			]
			},
			scaleR:{
			  refAngle:270
			},
			series : [
				{
				  text: "Sold",
					values : [800000],
					lineColor: "#00BAF2",
					backgroundColor: "#00BAF2",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#00BAF2'
					}
				},
				{
				  text: "Unsold",
					values : [56711],
					lineColor: "#E80C60",
					backgroundColor: "#c1bfc1",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#E80C60'
					}
				},
			]
		};
		 
		zingchart.render({ 
			id : 'solution_progress_two', 
		  data: {
			graphset: [myConfig]
		  },
			height: '250', 
			width: '99%' 
		});
   </script>
   <!-- 3rd circle progress -->
	<script>
		var myConfig = {
		backgroundColor:'#FBFCFE',
			type: "ring",
			title: {
			  text: "Solution 3",
			  fontFamily: 'Lato',
			  fontSize: 14,
			  // border: "1px solid black",
			  fontColor : "#1E5D9E",
			},
		
			plotarea: {
			  backgroundColor: 'transparent',
			  borderWidth: 0,
			  borderRadius: "0 0 0 10",
			  margin: "70 0 10 0"
			},
			legend : {
			toggleAction:'remove',
			backgroundColor:'#FBFCFE',
			borderWidth:0,
			adjustLayout:true,
			align:'center',
			verticalAlign:'bottom',
			marker: {
				type:'circle',
				cursor:'pointer',
				borderWidth:0,
				size:5
			},
			item: {
				fontColor: "#777",
				cursor:'pointer',
				offsetX:-6,
				fontSize:12
			},
			mediaRules:[
				{
					maxWidth:500,
					visible:false
				}
			]
			},
			scaleR:{
			  refAngle:270
			},
			series : [
				{
				  text: "Sold",
					values : [106541],
					lineColor: "#00BAF2",
					backgroundColor: "#00BAF2",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#00BAF2'
					}
				},
				{
				    text: "Unsold",
					values : [106000],
					lineColor: "#E80C60",
					backgroundColor: "#c1bfc1",
					lineWidth: 1,
					marker: {
					  backgroundColor: '#E80C60'
					}
				},
			]
		};
		 
		zingchart.render({ 
			id : 'solution_progress_three', 
		  data: {
			graphset: [myConfig]
		  },
			height: '250', 
			width: '99%' 
		});
   </script>
   <!-- end of 3rd circle progress-->
	<script>
		zingchart.THEME="classic";
		var myConfig = {
			"graphset": [
				{
					"type": "hbar3d",
					"background-color": "#FFF",
					"stacked": true,
					"3d-aspect": {
						"true3d": false,
						"y-angle": 10,
						"depth": 15
					},
					
					"legend": {
						"layout": "float",
						"margin":"12% auto auto auto",
						"background-color": "none",
						"border-width": 0,
						"shadow": 0,
						"toggle-action": "remove",
						"marker": {
							"border-color": "#fff"
						},
						"item": {
							"font-color": "#000"
						}
					},
					"tooltip": {
						"text": "%t / %k = %v<br>%k Total = %total",
						"font-color": "#000",
						"border-width": "1px",
						"border-color": "#ffffff"
					},
					"plot": {
						"bar-width": 25,
						"alpha": 0.9
					},
					"plotarea": {
						"background-color": "#FFF",
						"margin": "25% 5% 20% 15%"
					},
					"scale-x": {
						"values": [
							"APAC",
							"S. ASIA",
							"Americas",
							"EMEA"							
						],
						"background-color": "#4F678E",
						"guide": {
							"line-color": "#fff"
						},
						"tick": {
							"line-color": "#6e82a1"
						},
						"item": {
							"font-color": "#000",
							"offset-x": "-5%"
						}
					},
					"scale-y": {
						"background-color": "#43577c",
						
						"guide": {
							"line-color": "#fff"
						},
						"tick": {
							"line-color": "#6e82a1"
						},
						"item": {
							"font-color": "#000"
						}
					},
					"series": [
						{
							"values": [
								17,
								28,
								9,
								14,
								27,
								13
							],
							"background-color": "#00baf0",
							"text": "Solution 1"
						},
						{
							"values": [
								11,
								26,
								7,
								44,
								11,
								28
							],
							"background-color": "#8AB839",
							"text": "Solution 2"
						},
						{
							"values": [
								13,
								21,
								16,
								30,
								23,
								18
							],
							"background-color": "#FABE28",
							"text": "Solution 3"
						},
					]
				}
			]
		};
		 
		zingchart.render({ 
			id : 'graph_solution', 
			data : myConfig, 
			height: 400, 
			width: 800 
		});
	</script>
	<script> window.fcWidget.init({ token: "2f7024f1-229c-4632-b1aa-9c0acb300c6e", host: "https://wchat.freshchat.com" });</script>
  </body>
</html>
