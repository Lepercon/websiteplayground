<?php
include('database.php');

function find_nid(&$nodes, $search) {
	$result = array();

	if (is_numeric($search)) {							// catch if just a person id is passed
		$temp = $search;
		$search = array();
		$search['id'] = $temp;
	}

	if (isset($search['id'])) {							// looking for one match
		foreach ($nodes as $key => $node) {
			if (is_array($node)) {		
				foreach ($node as $key2 => $person) { 
					if ($key2 == $search['id']) {
						$result[] = $key;
					}
				}
			}
		}
	} else if (isset($search[0])) {						// passed in an array of people to check
		foreach ($nodes as $key => $node2) {			// invalid argument when only one record in aray
			$matches = 0;
			if (is_array($node2)) {
				foreach ($node2 as $key2 => $person) {
					foreach ($search as $search_person) {
						if ($key2 == $search_person['id']) {
							$matches = $matches + 1;
						}
					}
				}
			}
			if ($matches == count($search)) {
				$result[0] = $key;
			}
		}
	}


	if (count($result) >=2) {
		return $result;
	} else {
		return $result[0];
	}
}

function show_parents(&$nodes, $search, $depth) {
	$depth = $depth - 1;
	$parents = find_parents($search['id']);
	if (isset($parents[0])) {								// if they have parents
		$nid = find_nid($nodes, $parents);								
		if($depth >= 0) {									// is tree too big?
			$node_name = "";	
			if (!isset($nid)) {								// parent couple already on graph ?
				$nid = "";
				foreach ($parents as $parent) {
					$nid .= "n".$parent['id'];
				}

				foreach ($parents as $parent) {		
					$parent_node_id = "";	
					$temp = find_nid($nodes, $parent);		
					if (isset($temp)) {						// is one parent already on graph ?
						if (count($nodes[$temp]) == 1) {
							$parent_node_id = $nodes[$temp][$parent['id']]['parent_node'];
							unset($nodes[$temp]);			// remove single child if replacing with couple
						}
					}
						
					$nodes[$nid][$parent['id']]['name'] = $parent['name']. " " .str_replace("'", "", $parent['surname']);
					$nodes[$nid][$parent['id']]['year'] = ($search['yeargroup'] -1);
					$nodes[$nid][$parent['id']]['parent_node'] = $parent_node_id;
					if($parent['angel']!=0){$nodes[$nid][$parent['id']]['angel'] = $parent['angel'];}
				}
			}	
		} 
		if($depth >= -10) {									
			if (isset($nid)) {
				foreach ($parents as $parent) {	
					$grandparents_node_id = show_parents($nodes, $parent, $depth); 	//recurrance to grandparents
					$nodes[$nid][$parent['id']]['parent_node'] = $grandparents_node_id;
				}
			}
		}
	}
	return $nid;	
}

function show_children(&$nodes, $search, $depth) {
	$depth = $depth - 1;
	if($depth >= 0) {
		$children = find_children($search['id']);				// check database

		if (isset($children)) {									// if they have children
			foreach ($children as $child) {

				show_children($nodes, $child, $depth);			// recursion

				$parent_node_id = show_parents($nodes, $child,1);
				$nid = find_nid($nodes, $child);
				if (!isset($nid)) {								// child not already in node array
					$nid = "n".$child['id'];
					$nodes[$nid][$child['id']]['name'] = $child['name']. " " .str_replace("'", "", $child['surname']);
					if($child['angel']!=0){$nodes[$nid][$child['id']]['angel'] = $child['angel'];}
				}		

				if (is_array($nid)) {
					foreach ($nid as $temp_nid) {
						$nodes[$temp_nid][$child['id']]['parent_node'] = $parent_node_id;
					}
				} else {
					$nodes[$nid][$child['id']]['parent_node'] = $parent_node_id;
				}
			}
		}
	}
}

function show_me(&$nodes, $search) {
	$nid = find_nid($nodes, $search);

	if (!isset($nid)) {									// person not yet in the nodes array	
		$nid = count($nodes);
		$nodes[$nid][$search['id']]['name'] = $search['name']. " " .str_replace("'", "", $search['surname']);
		//$nodes[$nid][$search['id']]['year'] = $search['yeargroup'];
		if($search['angel']!=0){$nodes[$nid][$search['id']]['angel'] = $search['angel'];}
	}
	return $nid;
}

function show_siblings(&$nodes, $search) {
	$node_ids = array();

	$siblings = find_siblings($search['id']);

	foreach ($siblings as $sibling) {
		$nid = "n".$sibling['id'];
		$nodes[$nid][$sibling['id']]['name'] = $sibling['name']. " " .str_replace("'", "", $sibling['surname']);
		$nodes[$nid][$sibling['id']]['year'] = $sibling['yeargroup'];
		if($sibling['angel']!=0){$nodes[$nid][$search['id']]['sibling'] = $search['angel'];}
		$node_ids[] = $nid;
	}
	return $node_ids;
}

function show_re_marriages(&$dot, &$nodes) {
	// add green re-marriage link and remove the duplicate link to their parent.
	$temp = array();
	foreach ($nodes as $nid => $node2) {							// loop through all nodes 
		if (is_array($node2)) {
			foreach ($node2 as $key2 => $person) {					// loop through people in each node
				if (in_array($key2, $temp)) {						// check if person has already been seen
					$nids = find_nid($nodes, $key2);
					if ($nodes[$nids[0]][$key2]['year'] < $nodes[$nids[1]][$key2]['year']) {
						$dot .= $nids[0]. ":" . $key2 . "->" . $nids[1] . ":" . $key2 . ":w[color=green,weight=0];";
						unset($nodes[$nids[1]][$key2]['parent_node']);
					} else {
						$dot .= $nids[1]. ":" . $key2 . "->" . $nids[0] . ":" . $key2 . ":w[color=green,weight=0];";	
						unset($nodes[$nids[0]][$key2]['parent_node']);
					}
				}
				$temp[] = $key2;
			}
		}
	}
}

function make_dot(&$nodes) {
	$dot = "digraph g {";
	$dot .= "rankdir=LR; nodesep=0.12;";														//principal graph attributes
	$dot .= "node [shape=Mrecord,height=.1,fontsize=10,style=filled,fillcolor=lightblue];";		//principal node attributes

	foreach ($nodes as $nid => $node) {							// loop through each node to add to graph
		$node_text = "";
		if (is_array($node)) {	
			$node_colour = ""; 						
			foreach ($node as $key => $person) {				// loop through multiple people in node
				$node_text .= "<". $key .">";					// add ports to get arrows in correct place
				$node_text .= $person['name'] . "|";			// add the persons name	
				if(isset($person['angel'])){
					$node_colour = ",fillcolor=cyan";			// make cyan if they are a butler angel
				}				
				if(isset($person['highlight'])){
					$node_colour = ",fillcolor=".$person['highlight'];	// make red if they are the search person
				}
			}
			$node_text = substr($node_text, 0, -1);				// get rid of the extra "|" at the end
			$dot .=  $nid . "[label='". $node_text ."'".$node_colour."];";	// add node to $dot
		}
	}
	
	show_re_marriages($dot, $nodes);							// modify $dot and $nodes for re-marriages

	foreach ($nodes as $nid => $node) {							// loop through each node to add arrows 
		if(is_array($node)) {
			foreach ($node as $key => $person) {				// loop through multiple people in node
				if( isset($person['parent_node'])) {
					$dot .= $person['parent_node']."->".$nid.":".$key.":w;";	// add arrow from parent to child
				}
			}
		}
	}

	$dot .= "node[fillcolor=linen];";							// style for the years
	$dot .= "2005->2006->2007->2008->2009->2010->2011->2012->2013;";	// add years to top of graph

	$ranksame = array();
	foreach ($nodes as $nid => $node) {							// order nodes by yeargroup
		$year = 0;
		if (is_array($node)) {
			foreach ($node as $key => $person) {				// find youngest person in each node
				if ($person['year'] > $year) {$year = $person['year']; }
			}
		}
		if ($year != 0) {
			$ranksame[$year][] = $nid;
		}
	}
	foreach ($ranksame as $key => $year){
		$dot .= "{rank=same;". $key .";";
		foreach ($year as $nid) {
			$dot .= $nid.";";
		}
		$dot .= "}";

	}

	$dot .= "}";

	$dot = str_replace("'", "\"", $dot);					// convert double quotes
	return $dot;
}

function make_array($search, $children_depth, $parent_depth) {
	$nodes = array();
	$people = array();

	$sibling_nids = show_siblings($nodes, $search);	
	show_children($nodes, $search, $children_depth);
	$my_node_id = show_me($nodes, $search);
	$parent_node_id = show_parents($nodes, $search,$parent_depth);

	if (is_array($my_node_id)) {
		foreach ($my_node_id as $my_node_id_temp) {
			$nodes[$my_node_id_temp][$search['id']]['parent_node'] = $parent_node_id;
			$nodes[$my_node_id_temp][$search['id']]['highlight'] = "salmon";
		}
	} else {
		$nodes[$my_node_id][$search['id']]['parent_node'] = $parent_node_id;
		$nodes[$my_node_id][$search['id']]['highlight'] = "red";
	}
	
	foreach ($sibling_nids as $sibling_nid) {
		if(is_array($nodes[$sibling_nid])) {
			foreach ($nodes[$sibling_nid] as $key => $sibling) {
				$nodes[$sibling_nid][$key]['parent_node'] = $parent_node_id;
			}	
		}
	}
	return $nodes;
}

function show_tree($search) {
	if(strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false) {
		$depth = 1;
	} else { 
		$depth = 4;
	}

	$child_depth = $depth;
	$parent_depth = $depth;

	$nodes = make_array($search,$child_depth,$parent_depth);		// make nodes array based on search

	return make_dot($nodes);										// converts nodes array into a .dot string
}

if(isset($_POST['id'])) {
	echo show_tree(query("select * from people where id = '".$_POST['id']."'"));
} else if(isset($_POST['surname'])) {
	$search = query("select * from people where surname = '".$_POST['surname']."'");
	if(empty($search)) {
		echo 'nobody';
	} else {
		$nodes = array();
		if (is_array($search[0])) {
			foreach ($search as $person) {
				$nid = "n".$person['id'];
				$nodes[$nid][$person['id']]['name'] = $person['name']. " " .str_replace("'", "", $person['surname']);
				$nodes[$nid][$person['id']]['year'] = $person['yeargroup'];
			}
			echo make_dot($nodes);
		} else {
			echo show_tree($search);
		}
	}
} 

?>