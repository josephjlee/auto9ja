<?php

class Address_book_model extends MY_Model {
    
    const DB_TABLE = 'address_book';
    const DB_TABLE_PK = 'id';


	var $table = 'address_book';
	
    var $column_order = array(null, 'sender_email','recipient_name','recipient_email','date_added'); //set column field database for datatable orderable
	
    var $column_search = array('sender_email','recipient_name','recipient_email','date_added'); //set column field database for datatable searchable 
	
    var $order = array('id' => 'desc'); // default order 
    

    /**
     * sender_email.
     * @var string 
     */
    public $sender_email;

    /**
     * recipient_name.
     * @var string 
     */
    public $receiver_name;

    /**
     * recipient_email.
     * @var string 
     */
    public $receiver_email;

    /**
     * date_added.
     * @var string 
     */
    public $date_added;

		
	private function _get_datatables_query()
    {
         
        $this->db->from($this->table);
 
        $i = 0;
     
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
	
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }	
		
		
	
		/**
		* Function to insert address
		*  
		*/	
		public function add_address($data){
				
			$this->db->insert($this->table, $data);
			$insert_id = $this->db->insert_id();
			if ($insert_id){
				return true;
			}else {
				return false;
			}
				
		}
	
		/**
		* Function to update address_book
		* variable $id
		*/	
		public function update_address_book($data, $id){

			$this->db->where('id', $id);
			$query = $this->db->update($this->table, $data);
			
			if ($query){	
				return true;
			}else {
				return false;
			}			
			
		}

		/**
		* Function to check that the address 
		* exists in the database
		*/	
		public function unique_address($email,$recipient_name,$recipient_email){
			
			$this->db->where('sender_email', $email);
			$this->db->where('receiver_name', $recipient_name);
			$this->db->where('receiver_email', $recipient_email);
			
			$query = $this->db->get($this->table);
			
			if ($query->num_rows() == 0){
				return true;
			} else {
				return false;
			}
			
		}			
										
		/****
		** Function to get users address_book from the database
		**	variable $email
		****/
		function get_address_book($email){
			
			$this->db->where('sender_email', $email);
			$q = $this->db->get($this->table);
			
			if($q->num_rows() > 0){

			  foreach ($q->result() as $row){
				$data[] = $row;
			  }
			  return $data;
			}

		}			
			
			
			
			
			
			
			


	
    
}

