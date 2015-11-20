(function ($) {
    $.ballot = {
        init: function () {
            var availableTags = [];
            $('#users-list').children('p').each(function(){
                availableTags.push({value:$(this).text(),id:$(this).attr('value')});
            });
            $('.name-selection').autocomplete({
                source: availableTags,
                change: function(event, user){
                    if(user.item == null){
                        $(this).siblings('.user-id').val('');
                    }else{
                        $(this).siblings('.user-id').val(user.item.id);
                    }
                    $.ballot.guest_name();
                }
            });

            $.ballot.guest_name();
            $('.person-option select').change(function(){
                $.ballot.update_price($(this).closest('.person-option'));
            });

            $('#ballot_accordion').accordion({
                heightStyle:"content",
                active:+$('#active-tab').html()?+$('#active-tab').html():0
            });

            $( "#tabs" ).tabs();

            $.ballot.creation_buttons();

            $('.add-table').click(function(event){
                event.preventDefault();
                $('#tables').append($('<p><label></label><label>Table '+($('#tables').children().length+1)+': </label><select name="table[]"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14" selected="selected">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option><option value="32">32</option><option value="33">33</option><option value="34">34</option><option value="35">35</option><option value="36">36</option><option value="37">37</option><option value="38">38</option><option value="39">39</option><option value="40">40</option><option value="41">41</option><option value="42">42</option><option value="43">43</option><option value="44">44</option><option value="45">45</option><option value="46">46</option><option value="47">47</option><option value="48">48</option><option value="49">49</option><option value="50">50</option><option value="51">51</option><option value="52">52</option><option value="53">53</option><option value="54">54</option><option value="55">55</option><option value="56">56</option><option value="57">57</option><option value="58">58</option><option value="59">59</option><option value="60">60</option><option value="61">61</option><option value="62">62</option><option value="63">63</option><option value="64">64</option><option value="65">65</option><option value="66">66</option><option value="67">67</option><option value="68">68</option><option value="69">69</option><option value="70">70</option><option value="71">71</option><option value="72">72</option><option value="73">73</option><option value="74">74</option><option value="75">75</option><option value="76">76</option><option value="77">77</option><option value="78">78</option><option value="79">79</option><option value="80">80</option><option value="81">81</option><option value="82">82</option><option value="83">83</option><option value="84">84</option><option value="85">85</option><option value="86">86</option><option value="87">87</option><option value="88">88</option><option value="89">89</option><option value="90">90</option><option value="91">91</option><option value="92">92</option><option value="93">93</option><option value="94">94</option><option value="95">95</option><option value="96">96</option><option value="97">97</option><option value="98">98</option><option value="99">99</option></select></p>'));
                $('#tables').children().last().children('select').val($('#tables').children().eq(-2).children('select').val());
            });

            $('.remove-table').click(function(event){
                event.preventDefault();
                if($('#tables').children().length > 1){
                $('#tables').children().last().remove();
                }
            });
            
            if(($('#green-formal-alert').length > 0)){
                $("select[name$=0]").change(function(){
                    if(($('#green-formal-alert').attr('done') != 'done')){
                        sel = $(this);
                        if(sel.val() == '1'){
                            $('#green-formal-alert').children().dialog({
                                title: 'Are you sure?',
                                buttons:{
                                    "Ok!": function(){
                                        sel.val('0');
                                        $(this).dialog("close");
                                    },
                                    "No Thanks!": function(){
                                        $('#green-formal-alert').attr('done', 'done');
                                        $(this).dialog("close");
                                    }
                                },
                                modal: true,
                                width: 800
                            });
                        }
                    }
                });
            }

        },

        update_price: function(span_elem){
            
        },

        creation_buttons: function(){

            $('.add-option, .remove-option, .add-sub-option, .remove-sub-option').unbind('click');

            $('.add-option').click(function(event){
                event.preventDefault();
                $('#options').append($('<div><p><label></label><label>Option NUM Name: </label><input type="text" name="option[NUM][]" value="" placeholder="Option NUM Name" required="required"><a href="#" class="remove-sub-option jcr-button" title="Remove the last selection.">&nbsp;-&nbsp;</a><a href="#" class="add-sub-option jcr-button" title="Add a new selection.">&nbsp;+&nbsp;</a></p><p><label></label><label></label><label>Selection NUM-1: </label><input type="text" name="option[NUM][]" value="" placeholder="Selection NUM-1" required="required"> <input type="text" name="option[NUM][]" value="" placeholder="Price NUM-1" required="required"></p><p><label></label><label></label><label>Selection NUM-2: </label><input type="text" name="option[NUM][]" value="" placeholder="Selection NUM-2" required="required"> <input type="text" name="option[NUM][]" value="" placeholder="Price NUM-1" required="required"></p><p><label></label><label></label><label>Selection NUM-3: </label><input type="text" name="option[NUM][]" value="" placeholder="Selection NUM-3" required="required"> <input type="text" name="option[NUM][]" value="" placeholder="Price NUM-3" required="required"></p></div>'.split('NUM').join($('#options').children().length+1)));
                $.ballot.creation_buttons();
            });

            $('.remove-option').click(function(event){
                event.preventDefault();
                $('#options').children().last().remove();
            });

            $('.add-sub-option').click(function(event){
                event.preventDefault();
                $(this).parent().parent().append($('<p><label></label><label></label><label>Selection NUM-SUB: </label><input type="text" name="option[NUM][]" value="" placeholder="Selection NUM-SUB" required="required"> <input type="text" name="option[NUM][]" value="" placeholder="Price NUM-SUB" required="required"></p>'.split('NUM').join($(this).parent().parent().index()+1).split('SUB').join($(this).parent().siblings().length+1)));
            });

            $('.remove-sub-option').click(function(event){
                event.preventDefault();
                if($(this).parent().siblings().length > 2){
                    $(this).parent().siblings().last().remove();
                }
            });
        },

        guest_name: function(){

            $('.user-id').each(function(){
                if($(this).val() == '-1'){
                    $(this).siblings('.guest-name').attr('type', 'input').prop("required", true).attr('pattern', '[^\0]+ [^\0]+');
                }else{
                    $(this).siblings('.guest-name').attr('type', 'hidden').prop("required", false).attr('pattern', '');
                }
            });

        }
    }
})(jQuery);