<!DOCTYPE html>
<html>
<head>
<title>Agenda Generator</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<?php 
function addOrdinalNumberSuffix($num) {
	if ($num == 0)
	{
		return 'every';
	}
	else if (!in_array(($num % 100),array(11,12,13)))
	{
		switch ($num % 10) {
			// Handle 1st, 2nd, 3rd
			case 1:  return $num.'st';
			case 2:  return $num.'nd';
			case 3:  return $num.'rd';
		}
	}
	return $num.'th';
}
$mode = isset($mode) ? $mode : CONST_AGENDA;
$current_year = date("Y", time());
$selected_month = isset($month) ? $month : NULL;
?>
<!-- Bootstrap Core CSS -->
<link href="<?php echo base_url();?>asset/css/bootstrap.min.css"
	rel="stylesheet">
<link
	href="<?php echo base_url();?>asset/plugin/datepicker/datepicker3.css"
	rel="stylesheet">

<!-- Custom Fonts -->
<link
	href="<?php echo base_url();?>asset/fonts/font-awesome/css/font-awesome.min.css"
	rel="stylesheet" type="text/css">
<link
	href="<?php echo base_url();?>asset/plugin/jQuery-loading/jquery-loading.css"
	rel="stylesheet">
<link type="text/css" rel="stylesheet"
	href="<?php echo base_url();?>asset/plugin/timepicker/prettify.css" />
<link rel="stylesheet/less" type="text/css"
	href="<?php echo base_url();?>asset/plugin/timepicker/timepicker.less" />
	
<style>
.agenda {}
/* Dates */
.agenda .agenda-date {
	width: 170px;
}
.agenda .agenda-date .dayofmonth {
	width: 80px;
	font-size: 36px;
	line-height: 36px;
	float: left;
	text-align: right;
	margin-right: 10px;
}
.agenda .agenda-date .shortdate {
	font-size: 0.75em;
}
/* Times */
.agenda .agenda-time {
	width: 140px;
}
/* Events */
.agenda .agenda-events {}
.agenda .agenda-events .agenda-event {}
.form-horizontal .control-label.text-left {
	text-align: left;
}
@media ( max-width : 767px) {}
</style>
</head>
<body>
	<div class="container">
		<!-- setting -->
		<?php if ($mode == CONST_SETTING) : ?>
		<h2>Setting</h2>
		<div class="alert alert-warning">
			<h4>Supported Recurring Type</h4>
			<div>
				<span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp;WEEKLY
			</div>
			<div>
				<span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp;MONTHLY
			</div>
			<div>
				<span class="glyphicon glyphicon-check" aria-hidden="true"></span>&nbsp;IRREGULAR
			</div>
		</div>
		<section class="content-header">
		<div class="btn-group">
			<a class="btn btn-warning" href="<?php echo site_url("agenda");?>"> <span
				class="glyphicon glyphicon-home" aria-hidden="true"></span>
				&nbsp;Home
			</a>
			<button type="button" class="btn btn-warning" data-toggle="modal"
				data-target="#modalEditorForm"
				onclick="javascript:resetFormModal();">
				<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
				&nbsp;Add
			</button>
			<button type="button" class="btn btn-warning" data-toggle="modal"
				data-target="#modalGenerateForm">
				<span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
				&nbsp;Generate
			</button>
			<button type="button" class="btn btn-warning"
				onclick="javascript:deleteAction(null);">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
				&nbsp;Delete All
			</button>
		</div>
		</section>
		<section class="content">
		<div class="agenda">
			<div class="box">
			<div class="box-header">
				<h3 class="box-title">All Common Agenda</h3>
			</div>
			<div class="box-body">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>ID</th>
							<th>Start On</th>
							<th>Event & Place</th>
							<th>Material</th>
							<th>Repeat</th>
						</tr>
					</thead>
					<tbody>
					<?php
					if (isset($setting)) :
						foreach($setting as $row) :
							$start_on = $row->{CONST_START_ON};
							$start_on = $start_on == '0000-00-00' ? NULL : strtotime($start_on);
							$active = $start_on != NULL && time() == $start_on ? 'active' : '';
							$datetime = $row->{CONST_START_ON};
							$time  = isset($row->{CONST_TIME}) ? $row->{CONST_TIME} : NULL;
							$time .= $time != NULL && isset($row->{CONST_TIME_END}) ? ' - '.$row->{CONST_TIME_END} : NULL;
							$year = $start_on == NULL ? "" : date("Y", $start_on);
							$month = $start_on == NULL ? "" : date("F", $start_on);
							$month_and_year = (!empty($month) && !empty($year) ? $month.', '.$year : '');
							$date = $start_on == NULL ? "" : date("d", $start_on);
							$day = $start_on == NULL ? "" : date("l", $start_on);
							$repeat = "NO";
							$level = $row->{CONST_LEVEL} == CONST_LEVEL_KELOMPOK ? "" : "[{$row->{CONST_LEVEL}}] ";
							if ($row->{CONST_IS_REPEAT} == 1) :
								$repeat = "<b>" . $row->{CONST_FREQUENCY} . "</b>";
								if ($row->{CONST_FREQUENCY} == CONST_WEEKLY || $row->{CONST_FREQUENCY} == CONST_MONTHLY) :
									$repeat = $repeat . " <br /> " . implode(",", json_decode($row->{CONST_DAY}));
									$repeat = $repeat . " <br /> " . addOrdinalNumberSuffix($row->{CONST_WEEK}) . " week ";
								endif;
								if ($row->{CONST_FREQUENCY} == CONST_MONTHLY) :
									$repeat = $repeat . " <br /> every {$row->{CONST_PERIOD}} month (interval) ";
								endif;
								if ($row->{CONST_FREQUENCY} == CONST_IRREGULAR) :
									$repeat = $repeat . " <br /> " . implode(",", json_decode($row->{CONST_IRREGULAR_DATES}));
								endif;
							endif;
					?>
						<tr>
							<td class="agenda-date">
								&nbsp;#<?php echo $row->{CONST_ID}; ?>
								<a class="btn btn-danger btn-sm" href="#" onclick="javascript:deleteAction(<?php echo $row->{CONST_ID}; ?>);">
									<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
								</a>
								<a class="btn btn-primary btn-sm" href="#" onclick="javascript:editAction(<?php echo $row->{CONST_ID}; ?>,'modalEditorForm');">
									<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
								</a>
							</td>
							<td class="agenda-date <?php echo $active; ?>">
								<div class="dayofmonth"><i class="fa fa-calendar" aria-hidden="true">&nbsp;<?php echo $date; ?></i></div>
								<div class="dayofweek"><?php echo $day; ?></div>
								<div class="shortdate text-muted"><?php echo $month_and_year; ?></div>
								<div class="agenda-event"><?php echo $time; ?></div>
							</td>
							<td class="agenda-events">
								<div class="agenda-event">
									<b><?php echo $level . $row->{CONST_EVENT}; ?></b>
									<br /><i class="fa fa-map-marker" aria-hidden="true">&nbsp;<?php echo $row->{CONST_PLACE}; ?></i>
								</div>
							</td>
							<td class="agenda-events">
								<div class="agenda-event"><?php echo $row->{CONST_MATERIAL}; ?></div>
							</td>
							<td class="agenda-events active">
								<div class="agenda-event"><?php echo $repeat; ?></div>
							</td>
						</tr>
					<?php 
						endforeach;
					endif;
					?>
					</tbody>
				</table>
			</div>
			</div>
		</div>
		</section>
		<?php endif; ?>
		
		<!-- agenda -->
		<?php if ($mode == CONST_AGENDA) : ?>
		<h2>Agenda</h2>
		<div class="alert alert-warning">
			<p>This is all generated agendas</p>
			<p>Generated csv file can be imported to google calendar as task on the targeted account</p>
		</div>
		<section class="content-header">
		<div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">
			<div class="btn-group" role="group">
				<a class="btn btn-warning" href="<?php echo site_url($mode);?>"> <span
					class="glyphicon glyphicon-home" aria-hidden="true"></span>
					&nbsp;Home
				</a> <a class="btn btn-warning"
					href="<?php echo site_url("agenda/setting");?>"> <span
					class="glyphicon glyphicon-cog" aria-hidden="true"></span>
					&nbsp;Setup
				</a>
				<button type="button" class="btn btn-warning" data-toggle="modal"
					data-target="#modalEditorForm">
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
					&nbsp;Add
				</button>
				<button type="button" class="btn btn-warning"
					onclick="javascript:deleteAction(null);">
					<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
					&nbsp;Delete All
				</button>
				<button type="button" class="btn btn-warning" onclick="saveAsAgenda();">
					<i class="fa fa-file-excel-o" aria-hidden="true"></i>
					&nbsp;Save as CSV
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn btn-default">-Select Month-</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
				<?php 
				for($i = 0; $i <= 12; $i++)
				{
					if ($i == 0)
					{
						echo '<li><a href="'.site_url('agenda/list_data/'.$i.'/'.$current_year).'">All</a></li>';
					}
					else
					{
						echo '<li><a href="'.site_url('agenda/list_data/'.$i.'/'.$current_year).'">'.date('F',strtotime(date('Y').'-'.$i.'-01')).'</a></li>';
					}
				}
				?>
				</ul>
			</div>
		</div>
		</section>
		<section class="content">
		<div class="agenda">
			<div class="box">
			<div class="box-header">
				<h3 class="box-title">Agenda <?php echo  ($selected_month == 0 ? '' : ' in '.date('F',strtotime(date('Y').'-'.$selected_month.'-01'))) . ' ' . $current_year; ?></h3>
			</div>
			<div class="box-body">
			<div class="table-responsive">
				<table class="table table-condensed table-bordered">
					<thead>
						<tr>
							<th>ID</th>
							<th>Date</th>
							<th>Time & Place</th>
							<th>Event & Material</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					if (isset($agenda)) :
						foreach($agenda as $row) :
							$time  = isset($row->{CONST_TIME}) ? $row->{CONST_TIME} : NULL;
							$time .= $time != NULL && isset($row->{CONST_TIME_END}) ? ' - '.$row->{CONST_TIME_END} : NULL;
							$month = date("F", strtotime($row->{CONST_DATETIME}));
							$active = $row->{CONST_DATETIME} != NULL && time() == strtotime($row->{CONST_DATETIME}) ? 'active' : '';
							$date = $row->{CONST_DATETIME} == NULL ? "" : date("d", strtotime($row->{CONST_DATETIME}));
							$level = $row->{CONST_LEVEL} == CONST_LEVEL_KELOMPOK ? "" : "[{$row->{CONST_LEVEL}}] ";
					?>
						<tr>
							<td class="agenda-date">
								&nbsp;#<?php echo $row->{CONST_ID}; ?>
								<a class="btn btn-danger btn-sm" href="#" onclick="javascript:deleteAction(<?php echo $row->{CONST_ID};?>);">
									<span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
								</a>
								<a class="btn btn-primary btn-sm" href="#" onclick="javascript:editAction(<?php echo $row->{CONST_ID}; ?>,'modalEditorForm');">
									<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
								</a>
							</td>
							<td class="agenda-date <?php echo $active; ?>">
								<div class="dayofmonth"><i class="fa fa-calendar" aria-hidden="true">&nbsp;<?php echo $date; ?></i></div>
								<div class="dayofweek"><?php echo $row->{CONST_DAY}; ?></div>
								<div class="shortdate text-muted"><?php echo $month .', '. $row->{CONST_YEAR}; ?></div>
							</td>
							<td class="agenda-time">
								<?php echo $time;/*date('H:i', strtotime($row->{CONST_DATETIME}))*/ ?>
								<br /><i class="fa fa-map-marker" aria-hidden="true">&nbsp;<?php echo $row->{CONST_PLACE}; ?></i>
							</td>
							<td class="agenda-events">
								<div class="agenda-event">
									<b><?php echo $level . $row->{CONST_EVENT}; ?></b>
									<br /><i class="fa fa-book" aria-hidden="true">&nbsp;<?php echo $row->{CONST_MATERIAL}; ?></i>
								</div>
							</td>
						</tr>
					<?php 
						endforeach;
					endif;
					?>
					</tbody>
				</table>
			</div>
			</div>
			</div>
		</div>
		</section>
		<?php endif;?>
		
	</div>

	<div id="modalEditorForm" class="modal fade" tabindex="-1"
		role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Agenda Editor</h4>
				</div>
				<div class="modal-body bodyEditorForm">
					<form id="form-add-edit" role="form" class="form-horizontal">
						<input type="hidden" name="<?php echo CONST_ID;?>" />
						<div class="form-group level">
							<label class="col-sm-3 control-label" for="level">Level Event</label>
							<div class="col-sm-9">
								<select name="<?php echo CONST_LEVEL;?>"
									class="form-control">
									<option value="<?php echo CONST_LEVEL_DAERAH;?>">Daerah</option>
									<option value="<?php echo CONST_LEVEL_DESA;?>">Desa</option>
									<option value="<?php echo CONST_LEVEL_KELOMPOK;?>">Kelompok</option>
								</select>
							</div>
						</div>
						<div class="form-group event">
							<label class="col-sm-3 control-label" for="event">Event</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" maxlength="200"
									name="<?php echo CONST_EVENT;?>" />
							</div>
						</div>
						<div class="form-group place">
							<label class="col-sm-3 control-label" for="place">Place</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" maxlength="100"
									name="<?php echo CONST_PLACE;?>" />
							</div>
						</div>
						<div class="form-group material">
							<label class="col-sm-3 control-label" for="material">Material</label>
							<div class="col-sm-9">
								<input type="text" class="form-control" maxlength="100"
									name="<?php echo CONST_MATERIAL;?>" />
							</div>
						</div>
						<div class="form-group time">
							<label class="col-sm-3 control-label" for="time">Time Start</label>
							<div class="col-sm-9">
								<div class="input-group bootstrap-timepicker timepicker">
									<input type="text" class="form-control input-small timepicker"
										name="<?php echo CONST_TIME;?>" /> <span
										class="input-group-addon"> <span
										class="glyphicon glyphicon-time"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group time">
							<label class="col-sm-3 control-label" for="time_end">Time End</label>
							<div class="col-sm-9">
								<div class="input-group bootstrap-timepicker timepicker">
									<input type="text" class="form-control input-small timepicker"
										name="<?php echo CONST_TIME_END;?>" /> <span
										class="input-group-addon"> <span
										class="glyphicon glyphicon-time"></span>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group notes">
							<label class="col-sm-3 control-label" for="notes">Notes</label>
							<div class="col-sm-9">
								<textarea class="form-control editor-tools"
									name="<?php echo CONST_NOTES;?>"></textarea>
							</div>
						</div>
						<?php if ($mode == CONST_SETTING) :?>
						<div class="form-group date">
							<label class="col-sm-3 control-label" for="start_on">Start on</label>
							<div class="col-sm-9">
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker"
										name="<?php echo CONST_START_ON;?>" readonly="readonly" />
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php if ($mode == CONST_AGENDA) :?>
						<div class="form-group date">
							<label class="col-sm-3 control-label" for="start_on">Date</label>
							<div class="col-sm-9">
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker"
										name="<?php echo CONST_DATETIME;?>" readonly="readonly" />
								</div>
							</div>
						</div>
						<?php endif; ?>
						<?php if ($mode == CONST_SETTING) :?>
						<div class="form-group repeat">
							<label class="col-sm-3 control-label" for="repeat">Repeat</label>
							<div class="col-sm-9">
								<select name="<?php echo CONST_IS_REPEAT;?>"
									class="form-control repeat"
									onchange="javascript:evaluateRepeat('repeat')">
									<option value="0">Never</option>
									<option value="<?php echo CONST_WEEKLY;?>">Weekly</option>
									<option value="<?php echo CONST_MONTHLY;?>">Monthly</option>
									<option value="<?php echo CONST_IRREGULAR;?>">Irregular</option>
								</select>
							</div>
						</div>
						<div class="form-group day" style="display: none;">
							<label class="col-sm-3 control-label" for="day">Day</label>
							<div class="col-sm-9">
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-warning btn-su"> <input type="checkbox"
										class="day su" onchange="javascript:dayCheck('su')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_SUNDAY;?>" autocomplete="off" />Su
									</label> <label class="btn btn-warning btn-mo"> <input
										type="checkbox" class="day mo"
										onchange="javascript:dayCheck('mo')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_MONDAY;?>" autocomplete="off" />Mo
									</label> <label class="btn btn-warning btn-tu"> <input
										type="checkbox" class="day tu"
										onchange="javascript:dayCheck('tu')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_TUESDAY;?>" autocomplete="off" />Tu
									</label> <label class="btn btn-warning btn-we"> <input
										type="checkbox" class="day we"
										onchange="javascript:dayCheck('we')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_WEDNESDAY;?>" autocomplete="off" />We
									</label> <label class="btn btn-warning btn-th"> <input
										type="checkbox" class="day th"
										onchange="javascript:dayCheck('th')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_THURSDAY;?>" autocomplete="off" />Th
									</label> <label class="btn btn-warning btn-fr"> <input
										type="checkbox" class="day fr"
										onchange="javascript:dayCheck('fr')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_FRIDAY;?>" autocomplete="off" />Fr
									</label> <label class="btn btn-warning btn-sa"> <input
										type="checkbox" class="day sa"
										onchange="javascript:dayCheck('sa')"
										name="<?php echo CONST_DAY;?>[]"
										value="<?php echo CONST_SATURDAY;?>" autocomplete="off" />Sa
									</label>
								</div>
							</div>
						</div>
						<div class="form-group week" style="display: none;">
							<label class="col-sm-3 control-label" for="week"></label>
							<div class="col-sm-5">
								<select name="<?php echo CONST_WEEK;?>" class="form-control">
									<?php
									for($i = 0; $i <= 5; $i ++) {
										echo "<option value={$i}>".addOrdinalNumberSuffix($i)."</option>";
									}
									?>
								</select>
							</div>
							<label class="col-sm-4 control-label text-left period-label"
								for="week">Week</label>
						</div>
						<div class="form-group period" style="display: none;">
							<label class="col-sm-3 control-label" for="period"></label>
							<div class="col-sm-5">
								<select name="<?php echo CONST_PERIOD;?>" class="form-control">
									<?php
									for($i = 1; $i <= 12; $i ++) {
										echo "<option value={$i}>{$i}</option>";
									}
									?>
								</select>
							</div>
							<label class="col-sm-4 control-label text-left period-label"
								for="period-label">Month Interval</label>
						</div>
						<div class="form-group irregular" style="display: none;">
							<label class="col-sm-3 control-label" for="irregular_dates">Irregular Dates</label>
							<div class="col-sm-7">
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control datepicker"
										name="<?php echo CONST_DATE;?>" readonly="readonly" />
								</div>
							</div>
							<div class="col-sm-2">
								<button type="button" class="btn btn-warning btn_add_irregular_dates">
									<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
								</button>
							</div>
						</div>
						<div class="form-group irregular" style="display: none;">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
							<table class="table table-bordered table-striped table_irregular_dates">
								<thead>
									<tr>
										<th id="remove">#</th>
										<th>Selected Dates</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
							</div>
						</div>
						<?php endif; ?>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary"
						onclick="javascript:submitForm()">Save</button>
				</div>
			</div>
		</div>
	</div>

	<div id="modalGenerateForm" class="modal fade" tabindex="-1"
		role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Genarate Agenda</h4>
				</div>
				<div class="modal-body">
					<form id="form-generate-agenda" role="form" class="form-horizontal">
						<input type="hidden" name="id" />
						<div class="form-group">
							<label class="col-sm-3 control-label">Periode Agenda</label>
							<div class="col-sm-5">
								<select class="form-control" name="<?php echo CONST_MONTH; ?>" required>
									<option value="">--Pelase Select--</option>
									<?php 
									for($i = 1; $i <= 12; $i++)
									{
										echo "<option value={$i}>".date('F',strtotime(date('Y').'-'.$i.'-01'))."</option>";
									}
									?>
								</select>
							</div>
							<div class="col-sm-4">
								<select class="form-control" name="<?php echo CONST_YEAR; ?>" required=>
									<option value="">--Pelase Select--</option>
									<?php
									$start = $current_year - 0;
									$end = $current_year + 0;
									for($i = $start; $i <= $end; $i ++) {
										echo "<option value={$i}>{$i}</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group level">
							<label class="col-sm-3 control-label" for="level">Level Event</label>
							<div class="col-sm-9">
								<select name="<?php echo CONST_LEVEL;?>" class="form-control">
									<option value="">All</option>
									<option value="<?php echo CONST_LEVEL_DAERAH;?>">Daerah</option>
									<option value="<?php echo CONST_LEVEL_DESA;?>">Desa</option>
									<option value="<?php echo CONST_LEVEL_KELOMPOK;?>">Kelompok</option>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary"
						onclick="javascript:generateAgenda()">Generate</button>
				</div>
			</div>
		</div>
	</div>

	<script src="<?php echo base_url();?>asset/plugin/jQuery/jQuery-3.1.0.min.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/jQuery/jquery.serialize-object.min.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/bootstrap/bootstrap.min.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/datepicker/bootstrap-datepicker.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/moment/moment.min.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/bootbox/bootbox.min.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/jQuery-loading/jquery-loading.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/timepicker/prettify.js"></script>
	<script src="<?php echo base_url();?>asset/plugin/timepicker/bootstrap-timepicker.js"></script>

	<script type="text/javascript">
	var formId = 'form-add-edit';
	var formGenerate = 'form-generate-agenda';
	
	$(document).ready(function(){
		$.ajaxSetup({ cache: false });
		$(document).ajaxStart(function() {
   			startAJAXLoader();
   		}).ajaxStop(function() {
   			stopAJAXLoader();
   		}).ajaxError(function(e, jqxhr, settings, exception) {
   			if(jqxhr.responseJSON){
   				showErrorToDiv(jqxhr.responseJSON);
   			}
   			else{
   				bootbox.alert('System error occurred. Please refresh the browser and contact the system administrator.');
   			}
   		}).ajaxComplete(function(e, jqxhr, settings) {});
   		$(document).ajaxSend(function(e, xhr, options) {});
	});
	
	$(function() {
		$(".datepicker").datepicker({
			format			: '<?php echo CONST_DATE_MASK; ?>',
			minDate			: new Date(),
			todayHighlight	: 'TRUE',
		    startDate		: '-0d',
		    autoclose		: true,
		});
		
		$(".timepicker").timepicker({
			minuteStep: 1,
            template: 'modal',
            appendWidgetTo: 'body',
            showSeconds: true,
            showMeridian: false,
            defaultTime: false
		});
		
		prependDivError('div.modal-body.bodyEditorForm');
		
	});
	
	function submitForm(){
		var oForm = $('form#'+formId).serializeJSON();
		$.post('<?php echo site_url("agenda/save/".$mode); ?>', oForm).then(function(data){
    		var obj = JSON.parse(data);
    		if (obj.status == '<?php echo CONST_SUCCESS; ?>') {
    			window.location.reload();
    		} else {
    			showErrorToDiv(obj.errors);
    		}
      	});
    }
	
	function generateAgenda(){
		var oForm = $('form#'+formGenerate).serializeJSON();
		$.post('<?php echo site_url("agenda/generate/"); ?>', oForm).then(function(data){
    		var obj = JSON.parse(data);
    		if (obj.status == '<?php echo CONST_SUCCESS; ?>') {
    			window.location.reload();
    		} else {
    			showErrorToDiv(obj.errors);
    		}
      	});
	}
	
	function saveAsAgenda() {
		var url = '<?php echo site_url("agenda/save_as_csv/".$selected_month."/".$current_year); ?>';
		$.ajax({
			type: 'GET',
			url: url,
			processData: false,
			success: function(data) {
				window.location = url;
			},
			error: function (xhr) {
				console.log(' Error:  >>>> ' + JSON.stringify(xhr));
			}
		});
	}
	
	function deleteAction(id) {
		bootbox.confirm({
		    message: (id == null ? "Are you sure want to delete all ?" : "Are you sure want to delete this record ["+id+"] ?"),
		    buttons: {
		        confirm: {
		            label: 'Yes',
		            className: 'btn-danger'
		        },
		        cancel: {
		            label: 'No',
		            className: 'btn-success'
		        }
		    },
		    callback: function (result) {
		        if (result) {
		        	var s = '{"id":'+(id == null ? '"ALL"' : id)+'}';
		        	$.post('<?php echo site_url('agenda/delete/'.$mode); ?>', s).then(function(data){
		        		var obj = JSON.parse(data);
		        		if (obj.status == '<?php echo CONST_SUCCESS; ?>') {
		        			window.location.reload();
		        		} else {
		        			bootbox.alert("Ada error!");
		        		}
		          	});
		        }
		    }
		});
	}
	
	function editAction(id, modalId) {
		resetFormModal();
		$.getJSON('<?php echo site_url('agenda/detail/'.$mode.'/'); ?>'+id, function(data) {
			$('#'+modalId).modal('show');
			<?php if ($mode == CONST_AGENDA) :?>
			if (data.datetime) {
				data.datetime = moment(data.datetime,'<?php echo CONST_DATETIME_MASK_1; ?>').format('<?php echo CONST_DATE_MASK_1; ?>');
			}
			<?php endif; ?>
			populate('#'+formId, data);
			<?php if ($mode == CONST_SETTING) :?>
			if (data.<?php echo CONST_DAY; ?>) {
				var days = [];
				$.each(JSON.parse(data.day),function(key, value) {
					days.push(value);
				});
				$('.day').find(':checkbox[name="<?php echo CONST_DAY; ?>[]"]').each(function() {
					var match = ($.inArray($(this).val(), days) != -1);
					if (match) {
						$(this).trigger('click');
					}
			   	});
			}
			if (data.<?php echo CONST_IRREGULAR_DATES; ?>) {
				var startDates = JSON.parse(data.<?php echo CONST_IRREGULAR_DATES; ?>);
				for(var i=0;i<startDates.length;i++){
					var templateCheckbox = '<td><span class="glyphicon glyphicon-remove" aria-hidden="true" onclick ="delete_row($(this))"></span></td>';
					var date = "";
					var value = startDates[i];
					var hidden = '<input type="hidden" name="<?php echo CONST_IRREGULAR_DATES; ?>[]" value="'+value+'">';
					date = date + "<td class='newDate'>"+value+" "+hidden+"</td>";
					row = "<tr class='removeRow'>"+ templateCheckbox + date + "</tr>";
					$('.table_irregular_dates tbody').append(row);
				}
			}
			$('select.repeat').trigger('change');
			<?php endif; ?>
		});
	}
	
	function dayCheck(dayClass) {
		var btnClass = ".btn-" + dayClass;
		var checked = $("." + dayClass).is(':checked');
		if (checked) {
			$(btnClass).addClass("btn-primary").removeClass("btn-warning");
		} else {
			$(btnClass).addClass("btn-warning").removeClass("btn-primary");
		}
	}
	
	function evaluateRepeat(eleClass) {
		var oForm = $('select.'+eleClass);
		if (oForm.val() == 0 || oForm.val() == "0") {
			$('.day').hide();
	    	$('.week').hide();
	    	$('.period').hide();
	    	$('.irregular').hide();
	    } else {
	    	$('.day').show();
	    	$('.week').show();
	    	$('.period').hide();
	    	$('.irregular').hide();
		    if (oForm.val() == '<?php echo CONST_WEEKLY;?>') {
			} else if (oForm.val() == '<?php echo CONST_MONTHLY;?>') {
				$('.period').show();
			} else if (oForm.val() == '<?php echo CONST_IRREGULAR;?>') {
				$('.day').hide();
		    	$('.week').hide();
		    	$('.period').hide();
		    	$('.irregular').show();
		    }
	    }
	}
	
	$('.btn_add_irregular_dates').unbind('click').click(function(){
		var templateCheckbox = '<td><span class="glyphicon glyphicon-remove" aria-hidden="true" onclick ="delete_row($(this))"></span></td>';
		var value = $('[name="<?php echo CONST_DATE; ?>"]').val();
		var date = "";
		var row = "";
		if (value) {
			var same = false;
			$('.table_irregular_dates').find('tbody tr').each(function(){
				var dataDate = $(this).find('.newDate').text();
				var tableDate = new Date(dataDate).getTime();
				var valDate = new Date(value).getTime();
				if(tableDate===valDate){
					same = true;
					bootbox.alert("Selected Date already exists");
				}
			});
			if(!same){	
				var hidden = '<input type="hidden" name="<?php echo CONST_IRREGULAR_DATES; ?>[]" value="'+value+'">';
				date = date + "<td class='newDate'>"+value+" "+hidden+"</td>";
				row = "<tr class='removeRow'>"+ templateCheckbox + date + "</tr>";
				$('.table_irregular_dates tbody').append(row);
				$('[name="<?php echo CONST_DATE; ?>"]').val('');
			}
		} else {
			bootbox.alert("Please input start date first");
		}
	});
	
	function delete_row(row) {
        row.closest('tr').remove();
    }
	
	function resetFormModal() {
		$('#'+formId).trigger('reset');
		$('select.repeat').trigger('change');
		$('.removeRow').remove();
	}
	
	function populate(frm, data) {
		$.each(data, function(key, value){
    	    $('[name='+key+']', frm).val(value);
    	});
  	}
	
	function prependDivError(selector){
    	if($('#errorDiv').length==0){
    		$(selector).prepend('<div id="errorDiv" style="display:none;" class="alert alert-danger"></div>');
      	}
	}
	
	function showErrorToDiv(errors){
		clearErrorDiv();
    	var errorMessages = '<h4><i class="icon fa fa-ban"></i> Error!</h4>';
    	errorMessages = errorMessages + '<ul>';
    	for(var i=0;i<errors.length;i++){
    		errorMessages = errorMessages + '<li>';
    		errorMessages = errorMessages + errors[i];
    		errorMessages = errorMessages + '</li>';
    	}
    	errorMessages = errorMessages + '</ul>';
    	$('#errorDiv').append(errorMessages);
    	$('#errorDiv').css('display','');
	}
	
	function clearErrorDiv(){
		if($('#errorDiv').length>0){		
			$('#errorDiv').html('');
			$('#errorDiv').css('display','none');
		}
	}
	
	function startAJAXLoader(){
		$('.container').loading({ overlay: true,base: 0.6 });
	}

	function stopAJAXLoader(){
		$('.container').loading('hide');
	}
	
	</script>
</body>
</html>