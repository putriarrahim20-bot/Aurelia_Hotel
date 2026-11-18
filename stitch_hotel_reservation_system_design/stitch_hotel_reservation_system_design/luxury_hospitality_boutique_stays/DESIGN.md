---
name: Luxury Hospitality & Boutique Stays
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#45464d'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#76777d'
  outline-variant: '#c6c6cd'
  surface-tint: '#565e74'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#131b2e'
  on-primary-container: '#7c839b'
  inverse-primary: '#bec6e0'
  secondary: '#904d00'
  on-secondary: '#ffffff'
  secondary-container: '#fe932c'
  on-secondary-container: '#663500'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#2a1700'
  on-tertiary-container: '#b87500'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2fd'
  primary-fixed-dim: '#bec6e0'
  on-primary-fixed: '#131b2e'
  on-primary-fixed-variant: '#3f465c'
  secondary-fixed: '#ffdcc3'
  secondary-fixed-dim: '#ffb77d'
  on-secondary-fixed: '#2f1500'
  on-secondary-fixed-variant: '#6e3900'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
  surface-linen: '#FDFCFB'
  surface-sand: '#F8FAFC'
  surface-subtle: '#F1F5F9'
  border-hairline: '#E2E8F0'
  gold-light: '#FEF3C7'
  gold-muted: '#B45309'
typography:
  display:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '600'
    lineHeight: 56px
    letterSpacing: -0.02em
  display-mobile:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 36px
    fontWeight: '500'
    lineHeight: 44px
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 26px
    fontWeight: '500'
    lineHeight: 34px
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 20px
  label-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.04em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.06em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 1rem
  margin: 3rem
  margin-mobile: 1.25rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2.5rem
---

## Brand & Style
The design system establishes an atmosphere of curated luxury, tranquility, and bespoke architectural prestige for modern boutique resorts and high-end hotel reservations. 

Balancing timeless editorial elegance with effortless digital utility, the aesthetic draws heavily from **Modern Editorial Minimalism** mixed with **Tactile Warmth**. It eschews visual clutter in favor of generous whitespace, meticulous typographic contrast, micro-textures, and high-fidelity hospitality imagery. The interface must evoke relaxation, prestige, and intuitive clarity—assuring discerning travelers that their journey begins the moment they interact with the platform.

## Colors
The palette balances authoritative architectural depth with sun-drenched coastal tones and tactile linens:
- **Primary (`#0F172A`)**: Midnight Navy anchors headers, authoritative CTAs, critical booking summaries, and primary text, offering crisp readability without harsh pure-black contrast.
- **Secondary (`#D97706`) & Tertiary (`#F59E0B`)**: Warm Amber and Sunlit Gold accents are reserved for interactive focal points, selected states, badge treatments, rating stars, and premier service calls.
- **Surfaces (`#FDFCFB`, `#F8FAFC`, `#F1F5F9`)**: Soft linen and warm sand tones form backdrops and card containers, ensuring soft eye fatigue during evening browsing.
- **Borders & Dividers (`#E2E8F0`)**: Hairline dividers provide structure without breaking visual serenity.

## Typography
Typographic rhythm combines the grand, editorial character of **Playfair Display** with the functional clarity of **Plus Jakarta Sans**. 

- **Playfair Display**: Used for hero statements, suite naming, editorial pull quotes, and section anchors. It injects literary refinement and bespoke resort ambiance.
- **Plus Jakarta Sans**: Manages all interface elements, input forms, calendar matrices, pricing figures, and technical room specifications to ensure flawless legibility across small viewport screens.
- **Micro-Copy**: Labels, badges, and uppercase category markers leverage tracked-out `label-sm` or `label-md` for distinct architectural precision.

## Layout & Spacing
The layout follows a 12-column responsive fluid grid structured inside a centered maximum container of `1280px` for discovery feeds and `1440px` for full-screen immersive galleries.

- **Breakpoints**: 
  - Mobile: `< 768px` (Single column stacked flow, pinned bottom booking triggers).
  - Tablet: `768px - 1024px` (8-column reflow, side-by-side search bars).
  - Desktop: `> 1024px` (12-column layouts, sticky reservation summaries, two-column split checkout).
- **Rhythm**: Generous internal margins give high-end properties breathing room, avoiding dense budget-booking aggregator clutter. White space functions as a core luxury asset.

## Elevation & Depth
Depth is articulated through **tonal surface separation** backed by soft, ambient-cast shadows rather than harsh drop shadows.

- **Surface Layering**: The primary background rests on warm linen (`#FDFCFB`). Room cards, flyout calendars, and modal panels sit on pure white (`#FFFFFF`) framed by hairline borders (`#E2E8F0`).
- **Ambient Shadows**: Elevated panels (floating booking bar, sticky reservation card) utilize an ultra-diffused shadow tinted with Midnight Navy: `0 20px 40px -15px rgba(15, 23, 42, 0.07), 0 0 1px rgba(15, 23, 42, 0.08)`.
- **Active State Hover**: Room cards subtly elevate with an expanded vertical soft blur (+4px lift) and a border transition from subtle slate to warm amber.

## Shapes
A conservative, tailored geometry (`roundedness: 1`) reflects bespoke architectural lines:
- Interactive inputs, buttons, and cards utilize subtle `4px` (`rounded`) to `8px` (`rounded-lg`) corner radii.
- Pill badges (`9999px`) are strictly reserved for exclusive status indicators (e.g., "Villa Exclusive", "Michelin Key", "Last 2 Rooms").
- Image viewports maintain sharp or minimal 8px radii to preserve clean photographic framing.

## Components

### Booking & Search Bar
- **Desktop**: A unified, horizontal floating strip split into 4 modular segments: Destination/Resort, Date Range (Check-in/Check-out), Guests & Rooms, and a Primary Action button. Each segment features a micro-label (`label-sm`, tracking uppercase) over dynamic selected values.
- **Mobile**: Collapses into a sticky top-bar pill showing current search parameters, triggering an expansive full-screen overlay sheet on tap.

### Room Cards
- **Image Gallery Aspect**: Standardized 4:3 or 16:10 carousel frame with bottom-anchored pagination pips and top-left boutique rating badge (e.g., "5.0 Exceptional").
- **Content Hierarchy**: 
  - Room Title (`headline-sm`, Playfair Display).
  - Bed type, square footage, and key view amenities (Oceanfront, Private Pool) presented as inline text with hairline vertical splitters.
  - Price block aligned bottom-right: nightly rate highlighted in bold Midnight Navy (`headline-md`) alongside inclusive tax micro-labels.
- **Action**: Secondary outline button for "View Details" paired with a solid Primary button for "Reserve".

### Filter & Amenity Chips
- Horizontal scrolling pill array on mobile; segmented sidebar on desktop.
- Unselected: Linen background with `#E2E8F0` border and slate text.
- Selected: Deep Navy background with crisp white typography and subtle amber inner ring or micro checkmark.

### Price Summary Breakdown
- Sticky checkout sidebar component on desktop.
- Displays line-item nightly calculation, resort fees, local taxes, and bespoke add-ons (spa, breakfast, airport transfer).
- Clear, distinct separation between subtotal and total balance using hairline dividers and bolded `headline-sm` font.

### Selection Controls (Checkboxes & Radios)
- Custom square checkboxes (3px radius) and circular radios with hairline slate outlines, transitioning to deep navy fill and sharp white iconography upon selection.