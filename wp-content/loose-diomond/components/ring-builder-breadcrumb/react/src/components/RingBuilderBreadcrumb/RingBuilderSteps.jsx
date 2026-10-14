// ─────────────────────────────────────────────────────────────
// RingBuilderSteps.jsx
// Extracted from App4.jsx: the ring-builder breadcrumb's diamond
// step, setting step, and review step used to be duplicated inline
// (once for "diamond first" order, once for "setting first" order,
// plus the standalone review step). Pulled out into components
// here so App4.jsx just picks the order and renders each step once.
// Markup/behavior is unchanged from the original inline JSX.
// ─────────────────────────────────────────────────────────────
import React from 'react';

const CHECK_ICON_PATH = 'aj_custom/inventory/check.svg';

export function DiamondPlaceholderIcon() {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
      <g id="Group 117">
        <circle id="Ellipse 9" cx="24" cy="24" r="24" fill="white" />
        <path id="Diamond" fillRule="evenodd" clipRule="evenodd" d="M23.8819 38L8 18.2963L15.3482 10H32.4158L39.764 18.2963L23.8819 38ZM23.8819 36.4065L9.30892 18.3267L15.7984 11H31.9656L38.4551 18.3267L23.8819 36.4065Z" fill="#0B131A" />
      </g>
    </svg>
  );
}

export function SettingPlaceholderIcon() {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
      <g id="Group 108">
        <circle id="Ellipse 9" cx="24" cy="24" r="24" fill="white" />
        <path id="Setting" fillRule="evenodd" clipRule="evenodd" d="M19.3448 11.7333C19.1172 11.1838 19 10.5948 19 10H18.1765C18.1765 10.703 18.3149 11.399 18.5839 12.0485C18.8529 12.6979 19.2472 13.288 19.7443 13.7851C20.2414 14.2822 20.8315 14.6765 21.4809 14.9455C21.5889 14.9902 21.6981 15.0313 21.8084 15.0688C16.2572 15.8996 12 20.6879 12 26.4706C12 32.8381 17.1619 38 23.5294 38C29.8969 38 35.0588 32.8381 35.0588 26.4706C35.0588 20.6879 30.8016 15.8996 25.2504 15.0688C25.3607 15.0313 25.4699 14.9902 25.5779 14.9455C26.2273 14.6765 26.8174 14.2822 27.3145 13.7851C27.8116 13.288 28.2059 12.6979 28.4749 12.0485C28.7439 11.399 28.8824 10.703 28.8824 10H28.0588C28.0588 10.5948 27.9417 11.1838 27.714 11.7333C27.4864 12.2829 27.1528 12.7822 26.7322 13.2028C26.3116 13.6234 25.8123 13.957 25.2627 14.1846C24.7132 14.4123 24.1242 14.5294 23.5294 14.5294C22.9346 14.5294 22.3456 14.4123 21.7961 14.1846C21.2465 13.957 20.7472 13.6234 20.3266 13.2028C19.906 12.7822 19.5724 12.2829 19.3448 11.7333ZM34.2353 26.4706C34.2353 32.3833 29.4421 37.1765 23.5294 37.1765C17.6167 37.1765 12.8235 32.3833 12.8235 26.4706C12.8235 20.5579 17.6167 15.7647 23.5294 15.7647C29.4421 15.7647 34.2353 20.5579 34.2353 26.4706Z" fill="#0B131A" />
      </g>
    </svg>
  );
}

export function ReviewPlaceholderIcon() {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
      <g id="Group 108">
        <circle id="Ellipse 9" cx="24" cy="24" r="24" fill="white" />
        <path id="Union" fillRule="evenodd" clipRule="evenodd" d="M18.5989 10.8137L21.0089 8H26.4536L28.8496 10.797C29.1616 10.8012 29.4133 11.0554 29.4133 11.3684C29.4133 12.107 29.2679 12.8383 28.9852 13.5206C28.7026 14.203 28.2883 14.823 27.7661 15.3452C27.2438 15.8675 26.6238 16.2817 25.9415 16.5644C25.9116 16.5768 25.8816 16.5889 25.8515 16.6007C31.3795 17.5759 35.579 22.4028 35.579 28.2105C35.579 34.7217 30.3006 40 23.7895 40C17.2783 40 12 34.7217 12 28.2105C12 22.4029 16.1993 17.5761 21.7271 16.6008C21.697 16.5889 21.667 16.5768 21.637 16.5644C20.9547 16.2817 20.3347 15.8675 19.8124 15.3452C19.2902 14.823 18.8759 14.203 18.5933 13.5206C18.3107 12.8383 18.1652 12.107 18.1652 11.3684C18.1652 11.1003 18.3498 10.8753 18.5989 10.8137ZM20.0567 10.8686L23.731 14.3774L27.406 10.8685L25.9278 9.14286H21.5348L20.0567 10.8686ZM28.2628 11.6306L23.8444 15.8493C24.4141 15.8423 24.9774 15.7267 25.5041 15.5085C26.0478 15.2833 26.5418 14.9532 26.958 14.5371C27.3741 14.121 27.7042 13.627 27.9294 13.0833C28.121 12.6205 28.2336 12.1295 28.2628 11.6306ZM23.6144 15.8462L19.3243 11.7494C19.3634 12.2073 19.4727 12.6573 19.6492 13.0833C19.8744 13.627 20.2044 14.121 20.6206 14.5371C21.0367 14.9532 21.5307 15.2833 22.0744 15.5085C22.5641 15.7114 23.0856 15.8256 23.6144 15.8462ZM23.7895 38.8571C29.6695 38.8571 34.4361 34.0905 34.4361 28.2105C34.4361 22.3305 29.6695 17.5639 23.7895 17.5639C17.9095 17.5639 13.1429 22.3305 13.1429 28.2105C13.1429 34.0905 17.9095 38.8571 23.7895 38.8571Z" fill="#0B131A" />
      </g>
    </svg>
  );
}

// Selected-image + checkmark, or the placeholder icon when nothing's
// selected yet for that step.
function StepImage({ isSelected, imageUrl, title, templateUrl, PlaceholderIcon }) {
  return (
    <div className="ring_box position-relative">
      {isSelected && imageUrl ? (
        <>
          <img src={imageUrl} alt={title} width="48" height="48" />
          <img src={`${templateUrl}/${CHECK_ICON_PATH}`} className="br_checked" />
        </>
      ) : (
        <PlaceholderIcon />
      )}
    </div>
  );
}

// Step 1 or 2 content for the diamond leg of the flow: label/link
// when nothing's picked yet, the selected-diamond summary card once
// it is, and the step image on the right.
//
// `unselectedHref`: when the diamond step comes first (step 1) the
// "Choose a Diamond" label is never a link. When it comes second
// (step 2, after setting) it only becomes a link once the setting
// has been picked — pass that href in, or leave it undefined for a
// plain (unlinked) label.
export function DiamondStepContent({ diamond, urls, templateUrl, onClear, unselectedHref }) {
  return (
    <>
      {!diamond.is_selected ? (
        unselectedHref ? (
          <a className="text-decoration-none line-height-1" href={unselectedHref}>
            <span className="breadcrumb__title">Choose a Diamond</span>
          </a>
        ) : (
          <span className="breadcrumb__title">Choose a Diamond</span>
        )
      ) : (
        <div className="dmnd_prdct">
          <div className="pname">{diamond.title}</div>
          <div className="complete_prdct">
            <div className="prdct_price">${diamond.price}</div>
            <div className="d-flex">
              <button type="button" className="clear_rb" data-id="diamond" onClick={() => onClear('diamond')}>Clear</button>
              <a href={diamond.change_url} className="change_link">View</a>
            </div>
          </div>
        </div>
      )}

      <StepImage
        isSelected={diamond.is_selected}
        imageUrl={diamond.image_url}
        title={diamond.title}
        templateUrl={templateUrl}
        PlaceholderIcon={DiamondPlaceholderIcon}
      />
    </>
  );
}

// Step 1 or 2 content for the setting leg of the flow.
export function SettingStepContent({ setting, urls, templateUrl, onClear }) {
  return (
    <>
      {!setting.is_selected && (
        <a className="text-decoration-none line-height-1" href={urls.setting || '#'}>
          <span className={`breadcrumb__title ${setting.is_selected ? 'price-active' : ''}`}>Choose a Setting</span>
        </a>
      )}

      {setting.is_selected ? (
        <div className="setting_prdct">
          <div>
            <a href={setting.product_url} className="prdct pname">{setting.title}</a>
          </div>
          <div className="complete_prdct">
            <div className="prdct_price">${setting.price}</div>
            <div className="d-flex">
              <button type="button" className="clear_rb" data-id="setting" onClick={() => onClear('setting')}>Clear</button>
              <a href={setting.product_url} className="change_link">View</a>
            </div>
          </div>
        </div>
      ) : null}

      <StepImage
        isSelected={setting.is_selected}
        imageUrl={setting.image_url}
        title={setting.title}
        templateUrl={templateUrl}
        PlaceholderIcon={SettingPlaceholderIcon}
      />
    </>
  );
}

// Step 3: the completed-ring review/summary step.
export function ReviewStepContent({ setting, diamond, hasCompleteRing, urls, siteUrl, onClear }) {
  return (
    <>
      <div>
        {hasCompleteRing ? (
          <a className="text-decoration-none line-height-1" href={urls.preview || '#'}>
            <span className="breadcrumb__title price-active">Complete Ring</span>
          </a>
        ) : (
          <span className="breadcrumb__title">Complete Ring</span>
        )}

        {hasCompleteRing ? (
          <div className="complete_prdct">
            <div className="prdct_price">${Number(setting.price) + Number(diamond.price)}</div>
            <div className="d-flex">
              <button type="button" className="clear_rb" data-id="complete" onClick={() => onClear('complete')}>Clear</button>
              <a href={`${siteUrl}/ring-preview`} className="change_link">View</a>
            </div>
          </div>
        ) : null}
      </div>

      <div className="ring_box position-relative">
        <ReviewPlaceholderIcon />
      </div>
    </>
  );
}
