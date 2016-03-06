<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<div>
<?php
    ksort($tables);
    //echo nl2br(var_export($tables, true));
    $tab = explode(';', $b['tables']);
    
    $show_tables = false;
    switch($b['published']){
        case 'hidden':
            $show_tables = false;
            break;
        case 'unpublished':
            $show_tables = false;
            break;
        case 'check_correct':
            $show_tables = true;

            foreach($tables as $k=>$t){
                $table_correct = ($tab[$k-1]-count($t))==0;
                if((!$table_correct) && $this->ballot_admin){
                    echo '<p>Error on table '.$k.'</p>';
                }
                $show_tables = $show_tables && $table_correct;
            }
            break;
        case 'published':
            $show_tables = true;
            break;
            
    }
//var_dump($tables);
    if($show_tables){
        $i=1;
        $sports = array(
            0 => 'None',
            1 => 'Badminton',
            2 => 'Basketball',
            3 => 'Cheerleading',
            4 => 'Cricket',
            5 => 'Hockey',
            6 => 'Lacrosse',
            7 => 'Men’s Football',
            8 => 'Netball',
            9 => 'Pool/Darts',
            10 => 'Rounders',
            11 => 'Rowing',
            12 => 'Rugby',
            13 => 'Squash',
            14 => 'Table Tennis',
            15 => 'Tennis',
            16 => 'Ultimate Frisbee',
            17 => 'Volleyball',
            18 => 'Women’s Football',
            19 => 'Women’s Rugby',
            20 => 'Dodgeball'
        );
?>
        </div>
        <div id="tables-section">
            <div class="tables">
            <?php foreach($tables as $k=>$t){ ?>
                <div>
                    <p><b><?php echo ($b['close_time'] > time())?'Group '.$i++:'Table '.$k; ?>:</b></p>
                    <?php foreach($t as $key=>$p){  
                        if($b['id'] == 14){
                            $op = explode(';', $p['options']);
                            $sport = $sports[$op[2]];
                        }
                        ?>
                        <p title="<?php echo 'Signed Up By: '.($p['pn']==''?$p['fn']:$p['pn']).' '.$p['sn']; ?>"><?php echo ($key+1).'. '.($p['firstname']==''?$p['name']:($p['prefname']==''?$p['firstname']:$p['prefname']).' '.$p['surname']).($p['user_id']==-1?' ('.($p['pn']==''?$p['fn']:$p['pn']).' '.$p['sn'].')':'').($b['id']==14?' ['.$sport.']':''); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
            </div>
        </div>

<?php
    }else{
        echo '<p>We\'re really sorry but we don\'t have the result ready right now, please check back soon.</p>';
    }
?>