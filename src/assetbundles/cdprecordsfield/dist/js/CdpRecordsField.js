/**
 * MicroCDP plugin for Craft CMS
 *
 * MicroCDP JS
 *
 * @author    Disposition Tools
 * @copyright Copyright (c) 2020 Disposition Tools
 * @link      https://www.disposition.tools
 * @package   MicroCDP
 * @since     1.0.0
 */




 $('.newrecord').click(function(e){
     console.log("Open new Record form");

    var formContainer = $('#microcdpform' + $(this).data( "formtype" ));
    formContainer.show();
     /*
     e.preventDefault();
     var $form = $('#new-micro-cdp-record-form');
     var hud = new Garnish.HUD(e.target, $form,{onSubmit: checkForm });
     function checkForm() {
          $form.find('.submitbtn').trigger('click');
      }
      */
 });
