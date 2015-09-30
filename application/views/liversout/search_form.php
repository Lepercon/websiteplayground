<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$token = token_ip('property_search');?>
<div class="jcr-box wotw-outer search-form">
    <h2 class="wotw-day">Reviews</h2>
    <div>
        <p>Search reviews or search for a property to add a review:</p>
        <?php echo form_open('liversout', array('class' => 'no-jsify jcr-form')); ?>
            <p><?php echo anchor('liversout/search/area/all', 'View All Properties'); ?></p>
            <input type="search" placeholder="Postcode" maxlength="8" name="postcode">
            <input type="submit" value="Search Postcode">
            <input type="hidden" name="searchtype" value="postcode" />
            <?php echo $token;?>
        <?php echo form_close(); ?>
        <?php echo form_open('liversout', array('class' => 'no-jsify jcr-form')); ?>
            <input type="search" placeholder="Street" maxlength="30" name="address">
            <input type="submit" value="Search Street">
            <input type="hidden" name="searchtype" value="address" />
            <?php echo $token;?>
        <?php echo form_close(); ?>
        <?php echo form_open('liversout', array('class' => 'no-jsify jcr-form')); ?>
            <select name="area">
            <option value="all">All Areas</option>
            <option value="Claypath">Claypath</option>
            <option value="Elvet">Elvet</option>
            <option value="Gilesgate">Gilesgate</option>
            <option value="Nevilles Cross">Nevilles Cross</option>
            <option value="Viaduct">Viaduct</option>
            </select>
            <input type="submit" value="Search Area">
            <input type="hidden" name="searchtype" value="area" />
            <?php echo $token;?>
        <?php echo form_close(); ?>
        <p>Use any of the search fields above to find a property to add a review for, or to see what others have said about it. You can also upload photos of a property.</p>
        <a href="<?php echo site_url('liversout/add_property'); ?>" class="jcr-button inline-block" title="Add a property if it is not already listed">
            <span class="ui-icon ui-icon-plus inline-block"></span>Add Property
        </a>
        <p>Click an area name or property pin on the map. Pin locations are approximate.</p>
    </div>
</div>
