<?php
class Agenda_Model extends CI_Model 
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_datatable($table_source)
	{
		$search = $this->input->get(CONST_SEARCH);
		$offset = $this->input->get(CONST_START);
		$offset = $offset == NULL ? 0 : $offset;
		$limit = $this->input->get(CONST_LENGTH);
		$limit = $limit == NULL ? 10 : $limit;
		$order = $this->input->get(CONST_ORDER);
		$column = $this->__get_column_base_on_table_source($table_source);
		$orderColumn = isset($order[0][CONST_COLUMN]) ? $column[$order[0][CONST_COLUMN]] : CONST_MODIFICATION_TIME;
		$orderDirection = isset($order[0][CONST_DIR]) ? $order[0][CONST_DIR] : DESCENDING;
		$ordrBy = " order by ";
		$ordrByValue = $orderColumn . " " . $orderDirection;
		
		if (isset($search[CONST_VALUE]) && !empty($search[CONST_VALUE]))
		{
			$sql = "select * from ".$table_source." where column_name '%like%'" . $search[CONST_VALUE] . $ordrBy . $ordrByValue . " limit $offset,$limit";
			$sql_null = "select null from ".$table_source." where column_name '%like%'" . $search[CONST_VALUE] . $ordrBy . $ordrByValue;
			$result = $this->db->query($sql);
			$result_to_count = $this->db->query($sql_null);
			$count = $result_to_count->num_rows();
		}
		else
		{
			$sql = "select * from ".$table_source. $ordrBy . $ordrByValue . " limit $offset,$limit";
			$sql_null = "select null from ".$table_source. $ordrBy . $ordrByValue;
			$result = $this->db->query($sql);
			$result_to_count = $this->db->query($sql_null);
			$count = $result_to_count->num_rows();
		}
		$data = array();
		if (!empty($result->result()))
		{
			foreach ($result->result() as $row)
			{
				$data[] = $this->__fetch_data_base_on_table_source($table_source, $row);
			}
		}
		$results = array(
			CONST_DRAW => $this->input->get(CONST_DRAW),
			CONST_RECORDS_TOTAL => count($data),
			CONST_RECORDS_FILTERED => $count,
			CONST_DATA => $data
		);
		return json_encode($results);
	}
	
	public function get($table_source = NULL, $where = FALSE, $order = FALSE)
	{
		if ($table_source === NULL)
		{
			return FALSE;
		}
		if ($order !== NULL && is_array($order))
		{
			foreach ($order as $row => $value)
			{
				$this->db->order_by($row, $value);
			}
		}
		else
		{
			$this->db->order_by(CONST_DATE, ASCENDING);
		}
		if ($where === FALSE)
		{
			return $this->db->get($table_source)->result();
		}
		else
		{
			return $this->db->get_where($table_source, $where)->result();
		}
			
	}
	
	public function delete($table_source, $where = NULL)
	{
		if ($table_source != NULL)
		{
			if ($where !== NULL)
			{
				$tmp = $this->escape_data($where);
				$this->db->where($tmp);
			}
			$query = $this->db->delete($table_source);
			if ($query)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function save_or_update($table_source = NULL, $data = NULL, $where = NULL)
	{
		if ($where === NULL)
		{
			$this->__save_data($table_source, $data);
		}
		else
		{
			$this->__update_data($table_source, $data, $where);
		}
	}
	
	private function __save_data($table_source = NULL, $data = NULL)
	{
		if ($table_source != NULL && $data !== NULL)
		{
			$tmp = $this->escape_data($data);
			if ($this->db->insert($table_source, $tmp))
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	private function __update_data($table_source, $data, $where)
	{
		if ($table_source != NULL && $data !== NULL && $where !==NULL)
		{
			$tmp = $this->escape_data($where);
			$this->db->where($tmp);
			$tmp = $this->escape_data($data);
			$query = $this->db->update($table_source, $tmp);
			if ($query)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	private function __get_column_base_on_table_source($table_source)
	{
		$column = array();
		switch ($table_source) {
			case CONST_AGENDA:
				$column = array(
					CONST_ID,
					CONST_EVENT,
					CONST_TIME,
					CONST_PLACE,
					CONST_MATERIAL,
					CONST_NOTES,
					CONST_DATE_TIME,
					CONST_DAY,
					CONST_DATE,
					CONST_MONTH,
					CONST_YEAR
				);
			break;
			
			case CONST_SETTING:
				$column = array(
					CONST_ID,
					CONST_EVENT,
					CONST_TIME,
					CONST_PLACE,
					CONST_MATERIAL,
					CONST_NOTES,
					CONST_DATE_TIME,
					CONST_DAY,
					CONST_DATE,
					CONST_MONTH,
					CONST_YEAR,
					CONST_IS_REPEAT,
					CONST_FREQUENCY,
					CONST_PERIOD
				);
			break;
		}
		return $column; 
	}
	
	private function __fetch_data_base_on_table_source($table_source, $row)
	{
		$data = array();
		switch ($table_source) {
			case CONST_AGENDA:
				$data = array(
					CONST_ID => $row->{CONST_ID},
					CONST_EVENT => $row->{CONST_EVENT},
					CONST_TIME => $row->{CONST_TIME},
					CONST_PLACE => $row->{CONST_PLACE},
					CONST_MATERIAL => $row->{CONST_MATERIAL},
					CONST_NOTES => $row->{CONST_NOTES},
					CONST_DATE_TIME => $row->{CONST_DATE_TIME},
					CONST_DAY => $row->{CONST_DAY},
					CONST_DATE => $row->{CONST_DATE},
					CONST_MONTH => $row->{CONST_MONTH},
					CONST_YEAR => $row->{CONST_YEAR}
				);
				break;
					
			case CONST_SETTING:
				$data = array(
					CONST_ID => $row->{CONST_ID},
					CONST_EVENT => $row->{CONST_EVENT},
					CONST_TIME => $row->{CONST_TIME},
					CONST_PLACE => $row->{CONST_PLACE},
					CONST_MATERIAL => $row->{CONST_MATERIAL},
					CONST_NOTES => $row->{CONST_NOTES},
					CONST_DATE_TIME => $row->{CONST_DATE_TIME},
					CONST_DAY => $row->{CONST_DAY},
					CONST_DATE => $row->{CONST_DATE},
					CONST_MONTH => $row->{CONST_MONTH},
					CONST_YEAR => $row->{CONST_YEAR},
					CONST_IS_REPEAT => $row->{CONST_IS_REPEAT},
					CONST_FREQUENCY => $row->{CONST_FREQUENCY},
					CONST_PERIOD => $row->{CONST_PERIOD},
				);
				break;
		}
		return $data;
	}
	
}