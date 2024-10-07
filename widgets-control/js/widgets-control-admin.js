/**
 * widgets-control-admin.js
 *
 * Copyright (c) www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package widgets-control
 * @since 1.4.0
 */

jQuery(document).ready(function(){

	jQuery('.widgets-control-action').click(function(){
		var ajaxing = jQuery(this).data('ajaxing');
		if (!(typeof ajaxing === 'undefined' || !ajaxing)) {
			return;
		}
		jQuery(this).data('ajaxing',true);
		if (jQuery(this).hasClass('closed')) {
			jQuery(this).parent().parent().find('.widgets-control-sidebar-container').show();
			jQuery(this).removeClass('closed');
		} else {
			jQuery(this).parent().parent().find('.widgets-control-sidebar-container').hide();
			jQuery(this).addClass('closed');
		}
		jQuery.ajax({
			type   : 'POST',
			async  : false,
			url    : ajaxurl,
			data   : {
				action : 'widgets_control_sidebar_visibility',
				visibility : !jQuery(this).hasClass('closed'),
				widgets_control_ajax_nonce : widgets_control_ajax_nonce,
				sidebar : jQuery(this).attr('class')
			}
		});
		jQuery(this).data('ajaxing',false);
	});

	jQuery('.widgets-control-sidebar-save').click(function(){
		var ajaxing = jQuery(this).data('ajaxing');
		if (!(typeof ajaxing === 'undefined' || !ajaxing)) {
			return;
		}
		jQuery(this).data('ajaxing',true);

		if (
			( typeof ajaxurl !== 'undefined' ) &&
			( typeof widgets_control_ajax_nonce !== 'undefined' )
		) {
			var data = {
				action : 'widgets_control_sidebar_save',
				widgets_control_ajax_nonce : widgets_control_ajax_nonce,
				sidebars: {}
			};
			if (typeof widgets_control_sidebars !== 'undefined') {
				var sidebars_data = [];
				for (var i = 0; i < widgets_control_sidebars.length; i++) {
					var id = widgets_control_sidebars[i],
						pages = jQuery('textarea[name=\'widgets-control-sidebar['+id+'][pages]\']').val(),
						condition = jQuery('input[name=\'widgets-control-sidebar['+id+'][condition]\']:checked').val();
					sidebars_data.push({id:id,pages:pages,condition:condition});
				}
				data['sidebars'] = JSON.stringify(sidebars_data);
			}
			jQuery('.widgets-control-sidebar-buttons .spinner').addClass( 'is-active' );
			var response = jQuery.ajax({
				type   : 'POST',
				async  : false,
				url    : ajaxurl,
				data   : data
			}).done(function(data) {
				// successful, update field contents (text, radios)
				var sidebars = JSON.parse(data);
				if (typeof widgets_control_sidebars !== 'undefined') {
					var sidebars_data = [];
					for (var i = 0; i < widgets_control_sidebars.length; i++) {
						var id = widgets_control_sidebars[i];
						if (typeof sidebars[id] !== 'undefined') {
							if (typeof sidebars[id]['display'] !== 'undefined' ) {
								if (typeof sidebars[id]['display']['pages'] !== 'undefined') {
									var pages = sidebars[id]['display']['pages'];
									// stripslashes
									pages = pages.replace(/\\'/g,'\'').replace(/\\"/g,'"').replace(/\\0/g,'\0').replace(/\\\\/g,'\\');
									jQuery('textarea[name=\'widgets-control-sidebar['+id+'][pages]\']').val(pages);
								}
								if (typeof sidebars[id]['display']['condition'] !== 'undefined') {
									var condition = sidebars[id]['display']['condition'];
									switch(condition) {
										case '':
										case '1':
										case '2':
										break;
										default:
											condition = '';
									}
									// uncheck
									jQuery('input[name=\'widgets-control-sidebar['+id+'][condition]\']:not([value="'+condition+'"])').prop('checked', false);
									// check
									jQuery('input[name=\'widgets-control-sidebar['+id+'][condition]\'][value="'+condition+'"]').prop('checked',true);
								}
							}
						}
					}
				}
			});
		}
		jQuery(this).data('ajaxing',false);
		jQuery('.widgets-control-sidebar-buttons .spinner').removeClass( 'is-active' );
	});
});
