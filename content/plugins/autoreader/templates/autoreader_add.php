<div class="wrap">

<?php

    # Dependencies
    require_once(PLUGINS . 'autoreader/inc/tools.class.php' );
    require_once(PLUGINS . 'autoreader/helper/form.helper.php' );
    require_once(PLUGINS . 'autoreader/helper/edit.helper.php' );
    require_once(PLUGINS . 'autoreader/helper/tag.helper.php');

    require_once(PLUGINS . 'autoreader/autoreader.php');
    $arSettings = new Autoreader();   
    
    $action = $h->cage->get->testAlpha('action');
    $id = 0; // set to id of campaign we are editing 

    switch ($action) {
        case "edit":
            echo '<h2>Editing Campaign</h2>';
        // what $data settings are needed
         $data = $arSettings->campaign_structure;
            action_add($h, $data, $id);
            break;
        case "save" :
            $data = unserialize($_REQUEST['campaign']);
            $result = $arSettings->adminCampaignRequest($h, $data);
            //echo a json success or failure           
            break;
        default :
            echo '<h2>Add New Campaign</h2>';
            $data = $arSettings->campaign_structure;
            action_add($h, $data);
    }

   ?>

   
 <?php
function action_add($h, $data, $id=0) {
    global $action;
    global $arSettings;
 ?>
    <form id="edit_campaign" action="" method="post" accept-charset="utf-8">
      
      <?php if($action == 'edit'): ?>
           <input type="hidden" name="campaign_edit" id="campaign_edit" value="<?php echo $id; ?>" />
      <?php else: ?>
          <input type="hidden" name="campaign_add" id="campaign_add" value="1" />
      <?php endif; ?>

      <ul id="edit_buttons" class="submit">
        <li><a href="http://bloglinkjapan.com/wp-content/plugins/wp-o-matic/help.php?item=campaigns" class="help_link">Help</a></li>
        <li><input type="submit" name="edit_submit" value="Submit" id="edit_submit" /></li>
      </ul>

      <div id="admin_plugin_tabs">
      <ul class="tabs">
        <li class="current"><a href="#section_basic" id="tab_basic">Basic</a></li>
        <li><a href="#section_feeds" id="tab_feeds">Feeds</a></li>
        <li><a href="#section_categories" id="tab_categories">Categories</a></li>
        <li><a href="#section_rewrite" id="tab_rewrite">Rewrite</a></li>
        <li><a href="#section_options" id="tab_options">Options</a></li>
        <?php if($action == 'edit'): ?>
            <li><a href="#section_tools" id="tab_tools">Tools</a></li>
        <?php endif ?>
      </ul>
      </div>


      <div id="edit_sections">
        <!-- Basic section -->
        <div class="section current" id="section_basic">
          <div class="longtext required">
            <?php echo label_for('campaign_title', 'Title') ?>
            <?php echo input_tag('campaign_title', _data_value($data['main'], 'title')) ?>
            <p class="note">Tip: pick a name that is general for all the campaign's feeds (eg: Paris Hilton)</p>
          </div>

          <div class="checkbox required">
            <?php echo label_for('campaign_active', 'Active?') ?>
            <?php echo checkbox_tag('campaign_active', 1, _data_value($data['main'], 'active', true)) ?>
            <p class="note">If inactive, the parser will ignore these feeds</p>
          </div>

          <div class="text">
            <?php echo label_for('campaign_slug', 'Campaign slug') ?>
            <?php echo input_tag('campaign_slug', _data_value($data['main'], 'slug')) ?>
            <p class="note">Optionally, you can set an identifier for this campaign. Useful for detailed track of your ad-revenue.</p>
          </div>
        </div>

        <!-- Feeds section -->
        <div class="section" id="section_feeds">
          <p>Please fill in at least one feed. If you\'re not sure about the exact feed url, just type in the domain name, and the feed will be autodetected</p>

          <div id="edit_feed">
            <?php if(isset($data['feeds']['edit'])): ?>
              <?php foreach($data['feeds']['edit'] as $id => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_edit_' . $id, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[edit]['. $id .']', $feed, 'disabled=disabled class=input_text id=campaign_feed_edit_' . $id) ?>
                <?php echo checkbox_tag('campaign_feed[delete]['.$id.']', 1, (isset($data['feeds']['delete']) && _data_value($data['feeds']['delete'], $id)), 'id=campaign_feed_delete_' . $id) ?> <label for="campaign_feed_delete_<?php echo $id ?>" class="delete_label">Delete ?</label>
              </div>
              <?php endforeach ?>
            <?php endif ?>

            <?php if(isset($data['feeds']['new'])): ?>
              <?php foreach($data['feeds']['new'] as $i => $feed): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[new]['.$i.']', $feed, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>
              <?php endforeach ?>
            <?php else: ?>
              <?php for($i = 0; $i < 4; $i++): ?>
              <div class="inlinetext required">
                <?php echo label_for('campaign_feed_new_' . $i, 'Feed URL') ?>
                <?php echo input_tag('campaign_feed[new][]', null, 'class=input_text id=campaign_feed_new_' . $i) ?>
              </div>
              <?php endfor ?>
            <?php endif ?>
          </div>

          <a href="#add_feed" id="add_feed">Add more</a> | <a href="#" id="test_feeds">Check all) ?></a>
        </div>

        <!-- Categories section -->
        <div class="section" id="section_categories">
          <p>These are the categories where the posts will be created once they're fetched from the feeds.</p>
          <p>You have to select at least one.</p>

          <ul id="categories">
<?php // $arSettings->adminEditCategories($h, $data) ?>

            <?php if(isset($data['categories']['new'])): ?>
              <?php foreach($data['categories']['new'] as $i => $catname): ?>
              <li>
                <?php echo checkbox_tag('campaign_newcat[]', 1, true, 'id=campaign_newcat_' . $i) ?>
                <?php echo input_tag('campaign_newcatname[]', $catname, 'class=input_text id=campaign_newcatname_' . $i) ?>
              </li>
              <?php endforeach ?>
            <?php endif ?>
          </ul>

          <a href="#quick_add" id="quick_add">Quick add</a>
        </div>

        <!-- Rewrite section -->
        <div class="section" id="section_rewrite">
          <p>Want to transform a word into another? Or link a specific word to some website?
           <?php printf('<a href="%s" class="help_link">Read more</a>', $arSettings->helpurl . 'campaign_rewrite') ?></p>

          <ul id="edit_words">
            <?php if(isset($data['rewrites']) && count($data['rewrites'])): ?>
              <?php foreach($data['rewrites'] as $i => $rewrite): ?>
                <li class="word">
                  <div class="origin textarea">
                    <?php echo label_for('campaign_word_origin_' . $i, 'Origin') ?>
                    <?php echo textarea_tag('campaign_word_origin['.$i . ']', $rewrite['origin']['search'], 'id=campaign_word_origin_' . $rewrite->id) ?>
                    <label class="regex">
                      <?php echo checkbox_tag('campaign_word_option_regex['. $i .']', 1, $rewrite['origin']['regex']) ?>
                      <span><?php _e('RegEx', 'wpomatic') ?></span>
                    </label>
                  </div>

                  <div class="rewrite textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_rewrite['. $i .']', 1, isset($rewrite['rewrite'])) ?>
                      <span>Rewrite to:</span>
                    </label>
                    <?php echo textarea_tag('campaign_word_rewrite['. $i .']', _data_value($rewrite, 'rewrite')) ?>
                  </div>

                  <div class="relink textarea">
                    <label>
                      <?php echo checkbox_tag('campaign_word_option_relink['. $i .']', 1, isset($rewrite['relink'])) ?>
                      <span>Relink to:</span>
                    </label>
                    <?php echo textarea_tag('campaign_word_relink['. $i .']', _data_value($rewrite, 'relink')) ?>
                  </div>
                </li>
              <?php endforeach ?>
            <?php else: ?>
            <li class="word">
              <div class="origin textarea">
                <label for="campaign_word_origin_new1">Origin</label>
                <textarea name="campaign_word_origin[new1]" id="campaign_word_origin_new1"></textarea>
                <label class="regex"><input type="checkbox" name="campaign_word_option_regex[new1]" /> <span>RegEx</span></label>
              </div>
              <div class="rewrite textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_rewrite[new1]" /> <span>Rewrite to:</span></label>
                <textarea name="campaign_word_rewrite[new1]"></textarea>
              </div>
              <div class="relink textarea">
                <label><input type="checkbox" value="1" name="campaign_word_option_relink[new1]" /> <span>Relink to:</span></label>
                <textarea name="campaign_word_relink[new1]"></textarea>
              </div>
            </li>
            <?php endif ?>
          </ul>

          <a href="#add_word" id="add_word">Add more</a>
        </div>

        <!-- Options -->
        <div class="section" id="section_options">
          <?php if(isset($campaign_edit)): ?>
          <div class="section_warn">
            <img src="<?php echo $arSettings->tplpath ?>/images/icon_alert.gif" alt="Warning" class="icon" />
            <h3>Remember that</h3>
            <p>Changing these options only affects the creation of posts after the next time feeds are parsed.</p>
            <p>If you need to edit existing posts, you can do so by using the options under the Tools tab</p>
          </div>
          <?php endif ?>

          <div class="checkbox">
            <label for="campaign_templatechk">Custom post template</label>
            <?php echo checkbox_tag('campaign_templatechk', 1, _data_value($data['main'], 'template')) ?>

            <div id="post_template" class="textarea <?php if(_data_value($data['main'], 'template', '{content}') !== '{content}') echo 'current' ?>">
              <?php echo textarea_tag('campaign_template', _data_value($data['main'], 'template', '{content}')) ?>
              <a href="#" id="enlarge_link">Enlarge</a>

              <p class="note" id="tags_note">
                'Valid tags:
              </p>
              <p id="tags_list">
                <span class="tag">{content}</span>, <span class="tag">{title}</span>, <span class="tag">{permalink}</span>, <span class="tag">{feedurl}</span>, <span class="tag">{feedtitle}</span>, <span class="tag">{feedlogo}</span>,<br /> <span class="tag">{campaigntitle}</span>, <span class="tag">{campaignid}</span>, <span class="tag">{campaignslug}</span>
              </p>
            </div>

            <p class="note"><?php printf('Read about <a href="%s" class="help_link">post templates</a>, or check some <a href="%s" class="help_link">examples</a>',  $arSettings->helpurl . 'post_templates', $arSettings->helpurl . 'post_templates_examples') ?></p>
          </div>

          <div class="multipletext">
            <?php
              $f = _data_value($data['main'], 'frequency');

              if($f) {
                $frequency = WPOTools::calcTime($f);
              }
              else
                $frequency = array();
            ?>

            <label>Frequency</label>

            <?php echo input_tag('campaign_frequency_d', _data_value($frequency, 'days', 1), 'size=2 maxlength=3')?>
            d

            <?php echo input_tag('campaign_frequency_h', _data_value($frequency, 'hours', 5), 'size=2 maxlength=2')?>
            h

            <?php echo input_tag('campaign_frequency_m', _data_value($frequency, 'minutes', 0), 'size=2 maxlength=2')?>
            m

            <p class="note">How often should feeds be checked? (days, hours and minutes)</p>
          </div>

          <div class="checkbox">
            <?php echo label_for('campaign_cacheimages', 'Cache images') ?>
            <?php echo checkbox_tag('campaign_cacheimages', 1, _data_value($data['main'], 'cacheimages', is_writable($arSettings->cachepath))) ?>
            <p class="note">Images will be stored in your server, instead of hotlinking from the original site.
                <a href="<?php echo $arSettings->helpurl ?>image_caching" class="help_link">More</a></p>
          </div>

          <div class="checkbox">
            <?php echo label_for('campaign_feeddate', 'Use feed date') ?>
            <?php echo checkbox_tag('campaign_feeddate', 1, _data_value($data['main'], 'feeddate', false)) ?>
            <p class="note">Use the original date from the post instead of the time the post is created by WP-o-Matic.
                <a href="<?php echo $arSettings->helpurl ?>feed_date_option" class="help_link">More</a></p>
          </div>

          <div class="checkbox">
            <?php echo label_for('campaign_dopingbacks', 'Perform pingbacks') ?>
            <?php echo checkbox_tag('campaign_dopingbacks', 1, _data_value($data['main'], 'dopingbacks', false)) ?>
          </div>

          <div class="radio">
            <label class="main">Type of post to create</label>

            <?php echo radiobutton_tag('campaign_posttype', 'publish', !isset($data['main']['posttype']) || _data_value($data['main'], 'posttype') == 'publish', 'id=type_published') ?>
            <?php echo label_for('type_published', 'Published') ?>

            <?php echo radiobutton_tag('campaign_posttype', 'private', _data_value($data['main'], 'posttype') == 'private', 'id=type_private') ?>
            <?php echo label_for('type_private', 'Private') ?>

            <?php echo radiobutton_tag('campaign_posttype', 'draft', _data_value($data['main'], 'posttype') == 'draft', 'id=type_draft') ?>
            <?php echo label_for('type_draft', 'Draft') ?>
          </div>

          <div class="text">
            <?php echo label_for('campaign_author', 'Author:') ?>
            <?php echo select_tag('campaign_author', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>
            <p class="note">The created posts will be assigned to this author.</p>
          </div>

          <div class="text required">
            <?php echo label_for('campaign_max', 'Max items to create on each fetch') ?>
            <?php echo input_tag('campaign_max', _data_value($data['main'], 'max', '10'), 'size=2 maxlength=3') ?>
            <p class="note">Set it to 0 for unlimited. If set to a value, only the last X items will be selected, ignoring the older ones.</p>
          </div>

          <div class="checkbox">
            <?php echo label_for('campaign_linktosource', 'Post title links to source?') ?>
            <?php echo checkbox_tag('campaign_linktosource', 1, _data_value($data['main'], 'linktosource', false)) ?>
          </div>

          <div class="radio">
            <label class="main">Discussion options:</label>

            <?php echo select_tag('campaign_commentstatus',
                        options_for_select(
                          array('open' => 'Open',
                                'closed' => 'Closed',
                                'registered_only' => 'Registered only'
                                ), _data_value($data['main'], 'comment_status', 'open'))) ?>

            <?php echo checkbox_tag('campaign_allowpings', 1, _data_value($data['main'], 'allowpings', true)) ?>
            <?php echo label_for('campaign_allowpings', 'Allow pings') ?>
          </div>
        </div>

        <?php if(isset($campaign_edit)): ?>
        <!-- Tools -->
        <div class="section" id="section_tools">
          <div class="buttons">
            <h3>Posts action</h3>
            <p class="note">The selected action applies to all the posts created by this campaign</p>

            <ul>
              <li>
                <div class="btn">
                  <input type="submit" name="tool_removeall" value="Remove all" />
                </div>
              </li>
              <li>
                <div class="radio">
                  <label class="main">Change status to:</label>

                  <input type="radio" name="campaign_tool_changetype" value="publish" id="changetype_published" checked="checked" /> <label for="changetype_published">Published</label>
                  <input type="radio" name="campaign_tool_changetype" value="private" id="changetype_private" /> <label for="changetype_private">Private</label>
                  <input type="radio" name="campaign_tool_changetype" value="draft" id="changetype_draft" /> <label for="changetype_draft">Draft</label>
                  <input type="submit" name="tool_changetype" value="Change" />
                </div>
              </li>
              <li>
                <div class="text">
                  <label for="campaign_tool_changeauthor">Change author username to:</label>
                  <?php echo select_tag('campaign_tool_changeauthor', options_for_select($author_usernames, _data_value($data['main'], 'author', 'admin'))) ?>

                  <input type="submit" name="tool_changeauthor" value="Change" />
                </div>
              </li>
            </ul>
          </div>

          <!--
          <div class="btn">
            <label>Test all feeds</label>
            <input type="button" name="campaign_tool_testall_btn" value="Test" />
            <p class="note">This option creates one draft from each feed you added.</p>
          </div>
          -->
        </div>
        <?php endif; ?>
      </div>

    </form>



<?php

      };   ?>

      </div>

 <script type='text/javascript'>
    jQuery('document').ready(function($) {

        //$("#tab_container .tab_content").hide(); //Hide all content
        $("ul.tabs li:first").addClass("active").show(); //Activate first tab
        $(".tab_content:first").show(); //Show first tab content

        //On Click Event
        $("#admin_plugin_tabs ul.tabs li").click(function() {
            //alert('hi');
            $("ul.tabs li").removeClass("active"); //Remove any "active" class
            $(this).addClass("active"); //Add "active" class to selected tab
            $(".section").hide(); //Hide all tab content

            var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
            $(activeTab).show(); //Fade in the active ID content
            return false;
        });
     });

     $("#edit_submit").click(function(event) {
        event.preventDefault();

        // Save via AJAX
        //var item_uid = $('.post').attr('id');
        //   item_uid = item_uid.split('-');
        //   item_uid = item_uid[item_uid.length-1];  // this gets the id of the post from the class that WP adds in, gets last element on post id

        var campaign = $("form#edit_campaign").serialize();
        var formdata = 'campaign=' + campaign;
        var sendurl = BASEURL + 'admin_index.php?page=plugin_settings&plugin=autoreader&alt_template=autoreader_add&action=save';

        $.ajax(
            {
            type: 'post',
            url: sendurl,
            data: formdata,
            beforeSend: function () {
                            $('#edit_buttons').append('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/ajax-loader.gif' + '"/>');                           
                    },
            error: 	function(XMLHttpRequest, textStatus, errorThrown) {
                            //widget.html('ERROR');
            },
            success: function(data, textStatus) { // success means it returned some form of json code to us. may be code with custom error msg
                    if (data.error === true) {
                    }
                    else
                    {
                        var img_src = "";
                        // get required image based on returned data showing new status
                        if(data.enabled == 'true') { img_src = "active.png"; } else { img_src = "inactive.png"; }
                        $('#edit_buttons').html('<img src="' + BASEURL + "content/admin_themes/" + ADMIN_THEME + 'images/' + img_src + '"/>');
                    }
                    //$('#return_message').html(data.message).addClass(data.color);
                    //$('#return_message').html(data.message).addClass('message');
                    //$('#return_message').fadeIn(1000).fadeout(1000);
            },
            dataType: "json"
        });


        return false;
      
      });


function toSlug(str) {
    return str.replace(/\W/g, ' ').replace(/\ +/g, '-').replace(/\-$/g, '').replace(/^\-/g, '').toLowerCase();
  }


    // Basic tab
		$('#campaign_title').keyup(function() {
           // alert($('#campaign_title').val().replace(/ /g,'_'));
            $('#campaign_slug').val(toSlug( $('#campaign_title').val()));
		});




    // Feeds tab

		//- Test feed links
		function check_feed(feed) {
		  feed.addClass('input_text');
          if(feed.val().length > 0)
          {
            feed.addClass('green');
            var t = typeof t === 'string' ? t : t.responseText;
            if (t==1) { feed.addClass('ok input_text'); } else { feed.addClass('err input_text'); }
          };
          //jQuery.post("admin-ajax.php", {action: "test-feed", url: el.value, 'cookie': encodeURIComponent(document.cookie)}, oncomplete);
          feed.addClass('load input_text');
		};


    function update_feeds() {          
          $('#edit_feed div input[type=text]').focus(function() {
             $(this).addClass('input_text').addClass('red');              
          });

          $('#edit_feed div input[type=text]').blur(function() {
            check_feed($(this));
             $(this).removeClass('red');
            //alert('leave');
          });
        };

    update_feeds();




</script>