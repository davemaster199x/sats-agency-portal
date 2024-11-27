const smartwizardManager = (function ($) {
  let wizards = [];

  function smartwizardManager(wizardId, theme) {
    this.wizard = $('#' + wizardId);
    this.theme = theme;
    this.customButtons = [];
    wizards.push(this);
  }

  smartwizardManager.prototype = {
    initialize: function () {
      this.wizard.smartWizard({
        selected: 0,
        theme: this.theme,
        transitionEffect: {
          animation: 'fade',
        },
        enableUrlHash: false,
      });

      //Add custom buttons if any
      this.addCustomButtons();
    },
    addCustomButtons: function () {
      var self = this;

      if (this.customButtons.length > 0) {
        var customButtonContainer = this.wizard
          .parent()
          .find('.toolbar-bottom');

        $.each(this.customButtons, function (index, button) {
          var customButton = $('<button>')
            .text(button.text)
            .addClass(
              'btn btn-secondary sw-btn-custom sw-btn-group-extra w-100 custom-continue-btn customButton '
            )
            .click(function () {
              button.onClick.call(self);
            });

          // Add the custom class if provided
          if (button.customClass) {
            customButton.addClass(button.customClass);
          }

          customButtonContainer.append(customButton);
        });

        // Show the custom button only on the last step
        this.wizard.on(
          'showStep',
          function (e, anchor, stepNumber, stepDirection) {
            var totalSteps = self.wizard.smartWizard('getStepInfo').totalSteps;

            if (stepDirection === 'forward' || stepDirection === 'backward') {
              if (stepNumber === totalSteps - 1) {
                self.wizard.parent().find('.sw-btn-custom').show();
                self.wizard.parent().find('.sw-btn-next').hide();
              } else {
                self.wizard.parent().find('.sw-btn-custom').hide();
                self.wizard.parent().find('.sw-btn-next').show();
              }
            }
          }
        );
      }
    },
    addCustomButton: function (text, onClick, customClass) {
      this.customButtons.push({ text, onClick, customClass });
    },
  };

  return smartwizardManager;
})(jQuery);
