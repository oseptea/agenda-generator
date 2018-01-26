<?php
class Agenda extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('agenda_model', 'agenda_m');
	}
	
	public function index()
	{
		$month = date("m", time());
		$year = date("Y", time());
		return $this->list_data($month, $year);
	}
	
	public function list_data($month = NULL, $year = NULL)
	{
		$data['mode'] = CONST_AGENDA;
		if ($month !== NULL && $year !== NULL)
		{
			$where = array(CONST_YEAR => $year);
			if ($month != 0)
			{
				$where[CONST_MONTH] = $month;
			}
			$order = array(CONST_DATETIME => ASCENDING);
			$data[CONST_AGENDA] = $this->agenda_m->get(CONST_AGENDA, $where, $order);
			$data[CONST_MONTH] = $month;
		}
		$this->load->view("v_agenda", $data);
	}
	
	public function setting()
	{
		$order = array(CONST_START_ON => ASCENDING);
		$data[CONST_SETTING] = $this->agenda_m->get(CONST_SETTING, FALSE, $order);
		$data['mode'] = CONST_SETTING;
		$this->load->view("v_agenda", $data);
	}
	
	public function delete($source = NULL)
	{
		$results = array(CONST_STATUS => CONST_SUCCESS);
		$post = json_decode($this->security->xss_clean($this->input->raw_input_stream));
		if (!empty($post) && $source !== NULL)
		{
			$id = isset($post->{CONST_ID}) ? $post->{CONST_ID} : NULL;
			$where = $id == "ALL" ? array(1 => 1) : array(CONST_ID => $id);
			$this->agenda_m->delete($source, $where);
		}
		echo json_encode($results);
	}
	
	public function detail($source = NULL, $id = NULL)
	{
		$results = array(CONST_STATUS => CONST_SUCCESS);
		if ($this->input->is_ajax_request() && $source !== NULL && $id !== NULL)
		{
			$where = array(CONST_ID => $id);
			$data = $this->agenda_m->get($source, $where, FALSE);
			foreach ($data as $row)
			{
				$results = $this->__fetch_object($row);
			}
		}
		echo json_encode($results);
	}
	
	public function save($source)
	{
		$results = array(CONST_STATUS => CONST_SUCCESS);
		$post = json_decode($this->security->xss_clean($this->input->raw_input_stream));
		if ($this->input->is_ajax_request() && !empty($post) && $source !== NULL)
		{
			$id = $post->{CONST_ID};
			$where = (($id === NULL || $id === "") ? NULL : array(CONST_ID => $id));
			$data = $this->__prepare_save_data_base_on_table_source($source, $post);
			//Validation
			$errors = [];
			if(!isset($data[CONST_LEVEL]) || empty($data[CONST_LEVEL]))
			{
				$errors[] = "Level Event is mandatory";
			}
			if(!isset($data[CONST_EVENT]) || empty($data[CONST_EVENT]))
			{
				$errors[] = "Event is mandatory";
			}
			if(!isset($data[CONST_PLACE]) || empty($data[CONST_PLACE]))
			{
				$errors[] = "Place is mandatory";
			}
			if(!isset($data[CONST_MATERIAL]) || empty($data[CONST_MATERIAL]))
			{
				$errors[] = "Material is mandatory";
			}
			if($source == CONST_SETTING && !isset($data[CONST_IS_REPEAT]))
			{
				$errors[] = "Repeat is mandatory";
			} 
			if($source == CONST_SETTING && isset($data[CONST_IS_REPEAT]))
			{
				if(($data[CONST_IS_REPEAT] == 0 && empty($data[CONST_FREQUENCY])) && (!isset($data[CONST_START_ON]) || empty($data[CONST_START_ON])))
				{
					$errors[] = "Start on is mandatory";
				}
				if($data[CONST_FREQUENCY] == CONST_WEEKLY || $data[CONST_FREQUENCY] == CONST_MONTHLY)
				{
					if(!isset($data[CONST_DAY]) || empty($data[CONST_DAY]))
					{
						$errors[] = "Day is mandatory";
					}
					if($data[CONST_WEEK] == NULL)
					{
						$errors[] = "Week period is mandatory";
					}
					if($data[CONST_FREQUENCY] == CONST_MONTHLY && (!isset($data[CONST_PERIOD]) || empty($data[CONST_PERIOD])))
					{
						$errors[] = "Month interval is mandatory";
					}
				}
				if($data[CONST_FREQUENCY] == CONST_IRREGULAR)
				{
					if(!isset($data[CONST_IRREGULAR_DATES]) || empty($data[CONST_IRREGULAR_DATES]))
					{
						$errors[] = "Irregular Date is mandatory";
					}
				}
			}
			if($source == CONST_SETTING && $data[CONST_START_ON] != NULL)
			{
				if($data[CONST_START_ON] == '0000-00-00')
				{
					unset($data[CONST_START_ON]);
				}
			}
			if($source == CONST_AGENDA && empty($data[CONST_DATETIME]))
			{
				$errors[] = "Date is mandatory";
			}
			if (count($errors) > 0)
			{
				$results = array(
					CONST_STATUS => CONST_ERROR,
					CONST_ERRORS => $errors
				);
			}
			if (count($errors) == 0)
			{
				$this->agenda_m->save_or_update($source, $data, $where);
			}
		}
		echo json_encode($results);
	}
	
	public function generate()
	{
		$results = array(CONST_STATUS => CONST_SUCCESS);
		$post = json_decode($this->security->xss_clean($this->input->raw_input_stream));
		if ($this->input->is_ajax_request() && !empty($post))
		{
			$month = isset($post->{CONST_MONTH}) ? $post->{CONST_MONTH} : NULL;
			$year = isset($post->{CONST_YEAR}) ? $post->{CONST_YEAR} : NULL;
			$level = isset($post->{CONST_LEVEL}) ? $post->{CONST_LEVEL} : NULL;
			$start = 1;
			$end = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			$where = $level == NULL ? FALSE : array(CONST_LEVEL => $level);
			$master = $this->agenda_m->get(CONST_SETTING, $where, FALSE);
			if (count($master) > 0 && !empty($year) && !empty($month))
			{
				//override delete existing agenda
				$where = array(CONST_MONTH => $month, CONST_YEAR =>$year);
				if ($level != NULL)
				{
					$where[CONST_LEVEL] = $level;
				}
				$this->agenda_m->delete(CONST_AGENDA, $where);
				//LOOP setting
				foreach ($master as $row)
				{
					$data = $this->__fetch_object($row);
					if ($data[CONST_START_ON] != NULL)
					{
						$data[CONST_START_ON] = strtotime($data[CONST_START_ON]);
					}
					//prepare IRREGULAR type
					$irregular_dates = NULL; 
					if ($data[CONST_IRREGULAR_DATES] != NULL)
					{
						$data[CONST_IRREGULAR_DATES] = json_decode($data[CONST_IRREGULAR_DATES]);
						foreach ($data[CONST_IRREGULAR_DATES] as $key => $value)
						{
							$irregular_dates[$key] = strtotime($value);
						}
					}
					//prepare MONTHLY type
					$valid_month = FALSE;
					if ($data[CONST_FREQUENCY] === CONST_MONTHLY)
					{
						$p = $data[CONST_PERIOD];
						$arr_month = array();
						$i = 1;
						$a = TRUE;
						$m;
						$Y;
						do {
							$p = $p * $i;
							$m = date('n', strtotime('+ '.$p.' month', $data[CONST_START_ON]));
							$Y = date('Y', strtotime('+ '.$p.' month', $data[CONST_START_ON]));
							if ($Y === $year)
							{
								$arr_month[] = $m;
							}
							else 
							{
								$a = FALSE;
								break;
							}
							$i++;
						} while($a);
						if (in_array($month, $arr_month))
						{
							$valid_month = TRUE;
						}
					}
					for ($i = $start; $i <= $end; $i++)
					{
						$date = $i;
						//format: Y-m-d
						$full_date = $year.'-'.$month.'-'.$date;
						$week = $this->__weekOfMonth($date, $month, $year);
						$day = strtoupper($this->__dayOfDate($date, $month, $year));
						$datetime = strtotime($full_date);
						/** start on date base comparator **/
						if ($data[CONST_START_ON] != NULL && $data[CONST_START_ON] > $datetime)
						{
							//log_message('error', '#1');
							continue; //skip this condition
						}
						/** agenda that does not repeat **/
						if ($data[CONST_IS_REPEAT] === 0)
						{
							if ($data[CONST_START_ON] != $datetime)
							{
								//log_message('error', '#2');
								continue; //skip this condition
							}
						}
						else
						{
							$days = json_decode($data[CONST_DAY]);
							if ($data[CONST_FREQUENCY] === CONST_WEEKLY)
							{
								if(!(in_array($day, $days) && ($data[CONST_WEEK] == 0 || $data[CONST_WEEK] == $week)))
								{
									//log_message('error', '#3-$day:'.$day.'-$days:'.json_encode($days).'-$data[CONST_WEEK]:'.$data[CONST_WEEK].'-$week:'.$week);
									continue; //skip this condition
								}
							}
							if ($data[CONST_FREQUENCY] === CONST_MONTHLY)
							{
								if ($valid_month === FALSE || !(in_array($day, $days) && ($data[CONST_WEEK] == 0 || $data[CONST_WEEK] == $week)))
								{
									//log_message('error', '#4');
									continue; //skip this condition
								}
							}
							if ($data[CONST_FREQUENCY] === CONST_IRREGULAR)
							{
								if ($irregular_dates == NULL || empty($irregular_dates) || !in_array($datetime, $irregular_dates))
								{
									//log_message('error', '#5');
									continue; //skip this condition
								}
							}
						}
						$final = $data;
						//remove unused data
						unset($final[CONST_ID]);
						unset($final[CONST_START_ON]);
						unset($final[CONST_IS_REPEAT]);
						unset($final[CONST_PERIOD]);
						unset($final[CONST_FREQUENCY]);
						unset($final[CONST_IRREGULAR_DATES]);
						//completed minus data
						$final[CONST_DAY] = $day;
						$final[CONST_DATE] = $i;
						$final[CONST_MONTH] = $month;
						$final[CONST_YEAR] = $year;
						$final[CONST_WEEK] = $week;
						$final[CONST_DATETIME] = $full_date.' '.$final[CONST_TIME];
						//finally save record
						if (count($final) > 0)
						{
							$this->agenda_m->save_or_update(CONST_AGENDA, $final, NULL);
						}
					}
				}
			}
		}
		echo json_encode($results);
	}
	
	public function save_as_csv($month = NULL, $year = NULL)
	{
		if ($this->input->is_ajax_request())
		{
			return;
		}
		$header = array("Subject","Start Date","Start Time","End Date","End Time","All Day Event","Description","Location","Private");
		$data = array();
		$data[] = $header;
		/** prepare & format data **/
		if ($month !== NULL && $year !== NULL)
		{
			$where = FALSE;
			$order = array(CONST_DATETIME => ASCENDING);
			$master = $this->agenda_m->get(CONST_AGENDA, $where, $order);
			if (count($master) > 0 && !empty($year) && !empty($month))
			{
				foreach ($master as $row)
				{
					$temp = $this->__fetch_object($row);
					/**
					 * 1. Subject
					 * 2. Start Date
					 * 3. Start Time
					 * 4. End Date
					 * 5. End Time
					 * 6. All Day Event
					 * 7. Description
					 * 8. Location
					 * 9. Private
					 * **/
					$datetime = strtotime($temp[CONST_DATETIME]);
					$datetime_end = strtotime($temp[CONST_YEAR].'-'.$temp[CONST_MONTH].'-'.$temp[CONST_DATE].' '.$temp[CONST_TIME_END]);
					$descr  = '';
					$descr .= !empty($temp[CONST_MATERIAL]) ? 'Material: '.$temp[CONST_MATERIAL] : '';
					$descr .= !empty($temp[CONST_NOTES]) ? ' '.$temp[CONST_NOTES] : '';
					$content = array();
					$content[] = $temp[CONST_EVENT];
					$content[] = date('m/d/Y', $datetime);
					$content[] = date('H:i A', $datetime);
					$content[] = date('m/d/Y', $datetime);
					$content[] = date('H:i A', $datetime_end);
					$content[] = false;
					$content[] = $descr;
					$content[] = $temp[CONST_PLACE];
					$content[] = false;
					if (count($content) > 0)
					{
						$data[] = $content;
					}
				}
			}
		}
		//log_message('error', json_encode($data));
		$file_name = "tasks_import.csv";
		// Turn on output buffering
		ob_start();
		// Define handle to output stream
		$basic_info   = fopen("php://output", 'w');
		foreach ($data as $line) {
			fputcsv($basic_info, $line);
		}
		// Get size of output after last output data sent
		$stream_size = ob_get_length();
		//Close the filepointer
		fclose($basic_info);
		// Send the raw HTTP headers
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='.$file_name);
		header('Expires: 0');
		header('Cache-Control: no-cache');
		header('Content-Length: '. $stream_size);
		// Flush (send) the output buffer and turn off output buffering
		ob_end_flush();
	}
	
	private function __prepare_save_data_base_on_table_source($source, $post)
	{
		$data = array();
		$event = isset($post->{CONST_EVENT}) ? htmlentities($post->{CONST_EVENT}) : NULL;
		$place = isset($post->{CONST_PLACE}) ? htmlentities($post->{CONST_PLACE}) : NULL;
		$time = isset($post->{CONST_TIME}) ? htmlentities($post->{CONST_TIME}) : NULL;
		$time_end = isset($post->{CONST_TIME_END}) ? htmlentities($post->{CONST_TIME_END}) : NULL;
		$material = isset($post->{CONST_MATERIAL}) ? htmlentities($post->{CONST_MATERIAL}) : NULL;
		$notes = isset($post->{CONST_NOTES}) ? htmlentities($post->{CONST_NOTES}) : NULL;
		$is_repeat = isset($post->{CONST_IS_REPEAT}) ? $post->{CONST_IS_REPEAT} : NULL;
		$frequency = ($is_repeat == "0") ? NULL : $is_repeat;
		$start_on = isset($post->{CONST_START_ON}) ? $post->{CONST_START_ON} : NULL;
		$day = isset($post->{CONST_DAY}) ? $post->{CONST_DAY} : NULL;
		$date = isset($post->{CONST_DATE}) ? $post->{CONST_DATE} : NULL;
		$week = isset($post->{CONST_WEEK}) ? $post->{CONST_WEEK} : NULL;
		$month = isset($post->{CONST_MONTH}) ? $post->{CONST_MONTH} : NULL;
		$year = isset($post->{CONST_YEAR}) ? $post->{CONST_YEAR} : NULL;
		$period = isset($post->{CONST_PERIOD}) ? $post->{CONST_PERIOD} : NULL;
		$irregular_dates = isset($post->{CONST_IRREGULAR_DATES}) ? $post->{CONST_IRREGULAR_DATES} : NULL;
		$datetime = isset($post->{CONST_DATETIME}) ? $post->{CONST_DATETIME} : NULL;
		$level = isset($post->{CONST_LEVEL}) ? $post->{CONST_LEVEL} : NULL;
		switch ($source) {
			case CONST_SETTING:
				$data = array(
					CONST_EVENT => $event,
					CONST_TIME => $time,
					CONST_TIME_END => $time_end,
					CONST_PLACE => $place,
					CONST_MATERIAL => $material,
					CONST_NOTES => $notes,
					CONST_IS_REPEAT => ($is_repeat == "0") ? 0 : 1,
					CONST_FREQUENCY => $frequency,
					CONST_START_ON => $start_on,
					CONST_WEEK => $week,
					CONST_DAY => (is_array($day) ? json_encode($day) : $day),
					CONST_PERIOD => $period,
					CONST_IRREGULAR_DATES => (is_array($irregular_dates) ? json_encode($irregular_dates) : $irregular_dates),
					CONST_LEVEL => $level,
				);
				break;
				
			case CONST_AGENDA:
				$day = $datetime != NULL ? strtoupper(date('l', strtotime($datetime))) : NULL;
				$date = $datetime != NULL ? date('d', strtotime($datetime)) : NULL;
				$month = $datetime != NULL ? date('m', strtotime($datetime)) : NULL;
				$year = $datetime != NULL ? date('Y', strtotime($datetime)) : NULL;
				$week = $datetime != NULL ? $this->__weekOfMonth($date, $month, $year) : NULL;
				$data = array(
					CONST_EVENT => $event,
					CONST_TIME => $time,
					CONST_TIME_END => $time_end,
					CONST_PLACE => $place,
					CONST_MATERIAL => $material,
					CONST_NOTES => $notes,
					CONST_DAY => $day,
					CONST_DATE => $date,
					CONST_WEEK => $week,
					CONST_MONTH => $month,
					CONST_YEAR => $year,
					CONST_DATETIME => $datetime.' '.$time,
					CONST_LEVEL => $level,
				);
				break;
		}
		return $data;
	}
	
	private function  __fetch_object($row = NULL)
	{
		$data = array();
		if ($row !== NULL)
		{
			foreach ($row as $key => $value)
			{
				$data[$key] = $value;
			}
			if (isset($data[CONST_START_ON]))
			{
				$data[CONST_START_ON] = $data[CONST_START_ON] == '0000-00-00' ? NULL : $data[CONST_START_ON];
			}
			if (isset($data[CONST_IS_REPEAT]))
			{
				$data[CONST_IS_REPEAT] = $data[CONST_IS_REPEAT] == 1 ? $data[CONST_FREQUENCY] : 0;
			}
		}
		return $data;
	}
	
	private function __weekOfMonth($date, $month, $year)
	{
		$date = $year.'-'.$month.'-'.$date;
		$firstOfMonth = date("Y-m-01", strtotime($date));
		return intval(strftime("%U", strtotime($date))) - intval(strftime("%U", strtotime($firstOfMonth))) + 1;
	}
	
	private function __dayOfDate($date, $month, $year)
	{
		$d = $year.'-'.$month.'-'.$date;
		$date = DateTime::createFromFormat(CONST_DATE_MASK_2, $d);
		return $date->format('l');
	}
	
}