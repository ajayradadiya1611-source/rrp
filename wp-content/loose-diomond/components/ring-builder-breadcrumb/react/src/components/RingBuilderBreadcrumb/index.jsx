import React from 'react';
import PropTypes from 'prop-types';
import { DiamondStepContent, SettingStepContent, ReviewStepContent } from './RingBuilderSteps';

export default function App4({ currentPage = 'setting', rbData = {}, urls = {} }) {
  const {
    SITE_URL = '',
    templateUrl = '',
  } = window.LD_THEME || {};

  const setting = rbData.setting || {};
  const diamond = rbData.diamond || {};
  const isDiamondSkuPage = !!(
    typeof window !== 'undefined' &&
    window.LS_RING_BUILDER &&
    window.LS_RING_BUILDER.is_diamond_sku
  );
  const startedWith = rbData.started_with
    || (typeof window !== 'undefined' && window.LS_RING_BUILDER && window.LS_RING_BUILDER.started_with)
    || '';

  // Whichever the person picked first (diamond or setting) stays first in
  // the breadcrumb order, for the whole ring-building flow. Fall back to
  // the older selection-based guess only when we don't know which came
  // first yet (e.g. nothing selected, or older sessions without the flag).
  const diamondFirst = startedWith
    ? startedWith === 'diamond'
    : (
      isDiamondSkuPage
      || (diamond.is_selected && !setting.is_selected)
      || (currentPage === 'diamond' && !setting.is_selected)
    );

  const hasCompleteRing = !!(setting.is_selected && diamond.is_selected);

  const settingActive = `${currentPage === 'setting' ? 'active' : ''} ${setting.is_selected ? ' active' : ''}`;
  const diamondActive = `${currentPage === 'diamond' ? 'active' : ''} ${diamond.is_selected ? ' active' : ''}`;
  const reviewActive = `${currentPage === 'review' ? 'active' : ''} ${hasCompleteRing ? ' active' : ''}`;

  const adminAjax = (typeof window !== 'undefined' && window.LS_RING_BUILDER && window.LS_RING_BUILDER.admin_ajax_url)
    ? window.LS_RING_BUILDER.admin_ajax_url
    : '/wp-admin/admin-ajax.php';

  const onClear = (type) => {
    const body = new URLSearchParams();
    body.append('action', 'ls_rb_clear');
    body.append('type', type);
    console.log('clear_rb clicked: onClear');
    fetch(adminAjax, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: body
    })
      .then((res) => res.json())
      .then((data) => {
        if (data && data.status) {
          if (data.redirect_url) {
            window.location.href = data.redirect_url;
          } else {
            window.location.reload();
          }
        }
      })
      .catch((err) => console.error('ls_rb_clear error', err));
  };

  return (
    <div className="ringbuilder_brdcrmb">
      <div className="active d-none d-lg-block">
        <span className="breadcrumb__inner">
          <span className="breadcrumb__title breadcrumb__title_first">Build Your Ring</span>
        </span>
      </div>

      {/* Step 1 */}
      <span className={diamondFirst ? diamondActive.trim() : settingActive.trim()}>
        <span className="breadcrumb__inner">
          <span className="breadcrumb__number">1</span>
          {diamondFirst ? (
            <DiamondStepContent
              diamond={diamond}
              urls={urls}
              templateUrl={templateUrl}
              onClear={onClear}
            />
          ) : (
            <SettingStepContent
              setting={setting}
              urls={urls}
              templateUrl={templateUrl}
              onClear={onClear}
            />
          )}
        </span>
      </span>

      {/* Step 2 */}
      <span className={diamondFirst ? settingActive.trim() : diamondActive.trim()}>
        <span className="breadcrumb__inner">
          <span className="breadcrumb__number">2</span>
          {diamondFirst ? (
            <SettingStepContent
              setting={setting}
              urls={urls}
              templateUrl={templateUrl}
              onClear={onClear}
            />
          ) : (
            <DiamondStepContent
              diamond={diamond}
              urls={urls}
              templateUrl={templateUrl}
              onClear={onClear}
              unselectedHref={setting.is_selected ? (urls.diamond || '#') : undefined}
            />
          )}
        </span>
      </span>

      {/* Step 3 */}
      <span className={reviewActive.trim()}>
        <span className="breadcrumb__inner">
          <span className="breadcrumb__number">3</span>
          <ReviewStepContent
            setting={setting}
            diamond={diamond}
            hasCompleteRing={hasCompleteRing}
            urls={urls}
            siteUrl={SITE_URL}
            onClear={onClear}
          />
        </span>
      </span>
    </div>
  );
}

App4.propTypes = {
  currentPage: PropTypes.string,
  rbData: PropTypes.object,
  urls: PropTypes.object,
};
