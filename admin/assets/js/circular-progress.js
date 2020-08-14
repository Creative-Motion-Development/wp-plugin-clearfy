/**
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 04.02.2020, Webcraftic
 * @version 1.0
 */

//wfCircularProgress
jQuery.fn.wfCircularProgress = function (options) {
    jQuery(this).each(function () {
        var creationOptions;
        try {
            creationOptions = JSON.parse(jQuery(this).data('wfCircularProgressOptions'));
        } catch (e) { /* Ignore */
        }
        if (typeof creationOptions !== 'object') {
            creationOptions = {};
        }
        var opts = jQuery.extend({}, jQuery.fn.wfCircularProgress.defaults, creationOptions, options);

        var center = Math.floor(opts.diameter / 2);
        var insetRadius = center - opts.strokeWidth * 2;

        var circumference = 2 * insetRadius * Math.PI;
        var finalOffset = -(circumference * (1 - opts.endPercent));
        var initialOffset = -(circumference);

        var terminatorRadius = Math.floor(opts.strokeWidth * 1.5);
        var terminatorDiameter = 2 * terminatorRadius;
        var finalTerminatorX = center - insetRadius * Math.cos(Math.PI * 2 * (opts.endPercent - 0.25));
        var finalTerminatorY = center + insetRadius * Math.sin(Math.PI * 2 * (opts.endPercent - 0.25));
        var initialTerminatorX = center - insetRadius * Math.cos(Math.PI * 2 * (opts.startPercent - 0.25));
        var initialTerminatorY = center + insetRadius * Math.sin(Math.PI * 2 * (opts.startPercent - 0.25));

        var terminatorSVG = "m 0,-" + terminatorRadius + " a " + terminatorRadius + "," + terminatorRadius + " 0 1 1 0," + terminatorDiameter + " a " + terminatorRadius + "," + terminatorRadius + " 0 1 1 0,-" + terminatorDiameter;

        jQuery(this).data('wfCircularProgressOptions', JSON.stringify(opts));

        jQuery(this).css('width', opts.diameter + 'px');
        jQuery(this).css('height', opts.diameter + 'px');

        var svg = jQuery(this).find('svg');
        if (svg.length === 0) {
            svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            jQuery(this).append(svg);
        }
        var inactivePath = jQuery(this).find('.wclearfy-status-circle-inactive-path');
        if (inactivePath.length === 0) {
            inactivePath = document.createElementNS("http://www.w3.org/2000/svg", "path");
            jQuery(inactivePath).addClass('wclearfy-status-circle-inactive-path');
            jQuery(svg).append(inactivePath);
        }
        var activePath = jQuery(this).find('.wclearfy-status-circle-active-path');
        if (activePath.length === 0) {
            activePath = document.createElementNS("http://www.w3.org/2000/svg", "path");
            jQuery(activePath).addClass('wclearfy-status-circle-active-path');
            jQuery(svg).append(activePath);
        }
        var terminator = jQuery(this).find('.wclearfy-status-circle-terminator');
        if (terminator.length === 0) {
            terminator = document.createElementNS("http://www.w3.org/2000/svg", "path");
            jQuery(terminator).addClass('wclearfy-status-circle-terminator');
            jQuery(svg).append(terminator);
        }
        var text = jQuery(this).find('.wclearfy-status-circle-text');
        if (text.length === 0) {
            text = jQuery('<div class="wclearfy-status-circle-text"></div>');
            jQuery(this).append(text);
        }
        var pendingOverlay = jQuery(this).find('.wf-status-overlay-text');
        if (pendingOverlay.length === 0 && opts.pendingMessage.length !== 0) {
            pendingOverlay = jQuery('<div class="wclearfy-status-overlay-text"></div>');
            jQuery(this).append(pendingOverlay);
        }

        jQuery(svg).attr('viewBox', '0 0 ' + opts.diameter + ' ' + opts.diameter);
        jQuery(svg).css('display', opts.css_display);
        jQuery(svg).css('width', opts.diameter + 'px');
        jQuery(svg).css('height', opts.diameter + 'px');
        jQuery(inactivePath).attr('d', 'M ' + center + ',' + center + ' m 0,-' + insetRadius + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,' + (2 * insetRadius) + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,-' + (2 * insetRadius));
        jQuery(inactivePath).attr('stroke', opts.inactiveColor);
        jQuery(inactivePath).attr('stroke-width', opts.strokeWidth);
        jQuery(inactivePath).attr('fill-opacity', 0);
        jQuery(activePath).attr('d', 'M ' + center + ',' + center + ' m 0,-' + insetRadius + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,' + (2 * insetRadius) + ' a ' + insetRadius + ',' + insetRadius + ' 0 1 1 0,-' + (2 * insetRadius));
        jQuery(activePath).attr('stroke', opts.color);
        jQuery(activePath).attr('stroke-width', opts.strokeWidth);
        jQuery(activePath).attr('stroke-dasharray', circumference + ',' + circumference);
        jQuery(activePath).attr('stroke-dashoffset', initialOffset);
        jQuery(activePath).attr('fill-opacity', 0);
        jQuery(terminator).attr('d', 'M ' + initialTerminatorX + ',' + initialTerminatorY + ' ' + terminatorSVG);
        jQuery(terminator).attr('stroke', opts.color);
        jQuery(terminator).attr('stroke-width', opts.strokeWidth);
        jQuery(terminator).attr('fill', '#ffffff');
        jQuery(pendingOverlay).html(opts.pendingMessage);

        jQuery(pendingOverlay).animate({
            opacity: opts.pendingOverlay ? 1.0 : 0.0,
        }, {
            duration: 500,
            step: function (value) {
                var opacity = 1.0 - (value * 0.8);
                jQuery(svg).css('opacity', opacity);
                jQuery(text).css('opacity', opacity);
            },
            complete: function () {
                jQuery(svg).css('opacity', opts.pendingOverlay ? 0.2 : 1.0);
                jQuery(text).css('opacity', opts.pendingOverlay ? 0.2 : 1.0);
            }
        });

        jQuery(activePath).animate({
            "stroke-dashoffset": finalOffset + 'px'
        }, {
            duration: 500,
            step: function (value) {
                var percentage = 1 + value / circumference;
                var x = center - insetRadius * Math.cos(Math.PI * 2 * (percentage - 0.25));
                var y = center + insetRadius * Math.sin(Math.PI * 2 * (percentage - 0.25));
                jQuery(terminator).attr('d', 'M ' + x + ',' + y + ' ' + terminatorSVG);
                text.html(Math.round(percentage * 100));
            },
            complete: function () {
                text.html(Math.round(opts.endPercent * 100));
            }
        });
    });
};

jQuery.fn.wfCircularProgress.defaults = {
    startPercent: 0,
    endPercent: 1,
    color: '#16bc9b',
    inactiveColor: '#ececec',
    strokeWidth: 3,
    diameter: 100,
    pendingOverlay: false,
    pendingMessage: 'Note: Status will update when changes are saved',
    css_display: 'block',
};
