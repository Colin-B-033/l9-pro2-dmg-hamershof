<!doctype html>
<html lang="nl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Plattegrond Hamersveld</title>
  <style>
    * {
      box-sizing: border-box
    }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, sans-serif;
      background: #eef0f2;
      color: #1d2226
    }

    header {
      padding: 14px 16px
    }

    header h1 {
      margin: 0;
      font-size: 20px
    }

    header p {
      margin: 4px 0 0;
      color: #5a646d;
      font-size: 14px
    }

    .store.active {
      filter: brightness(1.12)
    }

    .hl {
      fill: none;
      stroke: #111;
      stroke-width: 6;
      stroke-linejoin: round;
      pointer-events: none;
      animation: pulse 1.2s ease-in-out infinite alternate
    }

    @keyframes pulse {
      from {
        stroke-opacity: 1
      }

      to {
        stroke-opacity: .35
      }
    }

    .tip {
      position: absolute;
      z-index: 5;
      pointer-events: none;
      background: #fff;
      border-radius: 8px;
      padding: 10px 14px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, .2);
      min-width: 150px;
      max-width: 240px
    }

    .tip h3 {
      margin: 0 0 3px;
      font-size: 15px
    }

    .tip p {
      margin: 0;
      font-size: 13px;
      color: #5a646d
    }

    .sheet {
      position: fixed;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 20;
      background: #fff;
      border-radius: 16px 16px 0 0;
      box-shadow: 0 -8px 30px rgba(0, 0, 0, .2);
      padding: 18px 20px 24px;
      transform: translateY(110%);
      transition: transform .25s ease;
      max-width: 560px;
      margin: 0 auto
    }

    .sheet.open {
      transform: none
    }

    .sheet h2 {
      margin: 0 0 6px;
      font-size: 19px
    }

    .sheet p {
      margin: 0;
      color: #5a646d
    }

    .sheet .close {
      position: absolute;
      top: 10px;
      right: 12px;
      border: 0;
      background: #eef0f2;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      font-size: 18px;
      cursor: pointer
    }

    .app {
      display: grid;
      grid-template-columns: 1fr 320px;
      gap: 16px;
      padding: 0 16px 16px;
      height: calc(100vh - 78px);
      min-height: 480px
    }

    .viewer {
      position: relative;
      background: #f8f8f8;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
      min-height: 0
    }

    .viewer-close,
    .viewer-open {
      display: none
    }

    .viewer svg {
      width: 100%;
      height: 100%;
      display: block;
      touch-action: none;
      cursor: grab;
      user-select: none
    }

    .viewer svg.dragging {
      cursor: grabbing
    }

    .controls {
      position: absolute;
      right: 12px;
      top: 12px;
      z-index: 4;
      display: flex;
      flex-direction: column;
      gap: 6px
    }

    .controls button {
      width: 40px;
      height: 40px;
      border: 0;
      border-radius: 8px;
      background: #fff;
      font-size: 20px;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0, 0, 0, .15)
    }

    /* Desktop: details-paneel over de onderkant van de kaart, niet over de lijst */
    .viewer .sheet {
      position: absolute;
      left: 12px;
      right: auto;
      bottom: 12px;
      width: min(380px, calc(100% - 24px));
      margin: 0;
      border-radius: 12px;
      transform: translateY(calc(100% + 24px))
    }

    .viewer .sheet.open {
      transform: none
    }

    aside {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
      display: flex;
      flex-direction: column;
      min-height: 0
    }

    .filters {
      padding: 12px 12px 6px;
      display: flex;
      flex-direction: column;
      gap: 8px;
      border-bottom: 1px solid #eef0f2
    }

    .filters input[type=search] {
      padding: 10px 12px;
      border: 1px solid #cfd5da;
      border-radius: 8px;
      font: inherit
    }

    .filters label {
      font-size: 14px;
      color: #5a646d;
      display: flex;
      gap: 8px;
      align-items: center;
      cursor: pointer
    }

    .count {
      font-size: 13px;
      color: #5a646d;
      padding: 6px 14px 0
    }

    aside ul {
      list-style: none;
      margin: 0;
      padding: 4px 6px 8px;
      overflow: auto;
      flex: 1
    }

    aside li button {
      display: flex;
      gap: 10px;
      align-items: center;
      width: 100%;
      text-align: left;
      border: 0;
      background: none;
      padding: 9px 8px;
      border-radius: 6px;
      font: inherit;
      cursor: pointer
    }

    aside li button:hover {
      background: #f3f5f6
    }

    aside li button.active {
      background: #e3e8ec;
      box-shadow: inset 3px 0 0 #1d2226
    }

    aside .sw {
      width: 14px;
      height: 14px;
      border-radius: 3px;
      flex: none
    }

    aside .n {
      font-weight: 600;
      min-width: 34px
    }

    aside small {
      color: #5a646d
    }

    .empty {
      padding: 16px;
      color: #5a646d;
      font-size: 14px
    }

    @media (max-width:1000px) {
      .app {
        grid-template-columns: 1fr;
        height: auto;
        padding: 0 8px 8px;
        gap: 8px
      }

      .viewer {
        height: 60vh
      }

      aside ul {
        max-height: 60vh
      }

      .viewer .sheet {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        width: auto;
        margin: 0 auto;
        max-width: 560px;
        border-radius: 16px 16px 0 0;
        transform: translateY(110%)
      }

      .viewer-close {
        display: grid;
        place-items: center;
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 5;
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 999px;
        background: #fff;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .15)
      }

      .viewer-open {
        display: none;
        position: absolute;
        inset: 0;
        z-index: 3;
        border: 0;
        background: rgba(248, 248, 248, .9);
        backdrop-filter: blur(1px);
        color: #1d2226;
        font: inherit;
        padding: 20px;
        cursor: pointer;
        text-align: center;
      }

      .viewer-open strong {
        display: block;
        font-size: 18px;
        margin-bottom: 6px
      }

      .viewer.is-collapsed {
        height: 120px
      }

      .viewer.is-collapsed svg,
      .viewer.is-collapsed .controls,
      .viewer.is-collapsed .tip,
      .viewer.is-collapsed .sheet {
        display: none
      }

      .viewer.is-collapsed .viewer-open {
        display: grid;
        place-items: center
      }
    }
  </style>
</head>

<body>
  <header>
    <h1>Plattegrond</h1>
    <p>Scroll of knijp om te zoomen, sleep om te pannen, of kies een winkel in de lijst.</p>
  </header>
  <div class="app">
    <div class="viewer">
      <button type="button" class="viewer-close" aria-label="Sluit plattegrond">&times;</button>
      <button type="button" class="viewer-open" aria-label="Open plattegrond"><span><strong>Tik om de plattegrond te
            openen</strong>Daarna kun je in- en uitzoomen en pannen.</span></button>
      <svg id="map" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2000 1427" aria-label="Plattegrond De Hamershof">
<style>.store{fill-opacity:0;stroke:transparent;cursor:pointer}.store:hover,.store:focus,.store.active,.store.is-selected{fill-opacity:.28;stroke:#163e50;stroke-width:5;outline:none}.hl{fill:none;stroke:#163e50;stroke-width:6;pointer-events:none}</style>
<image href="/images/plattegrond-pdf.svg" width="2000" height="1427" pointer-events="none"/>
<polygon class="store" data-id="91" tabindex="0" role="button" aria-label="Winkelvak 91" fill="#0097bc" points="643.69,342.67 643.76,285.05 592.22,285.05 592.22,342.71 643.69,342.67"/>
<polygon class="store" data-id="87b" tabindex="0" role="button" aria-label="Winkelvak 87b" fill="#0097bc" points="592.22,535.84 592.22,590.41 643.69,590.41 643.69,535.84 592.22,535.84"/>
<polygon class="store" data-id="83" tabindex="0" role="button" aria-label="Winkelvak 83" fill="#0097bc" points="763.93,674.94 763.93,593.07 711.84,593.07 711.84,674.94 763.93,674.94"/>
<polygon class="store" data-id="81" tabindex="0" role="button" aria-label="Winkelvak 81" fill="#0097bc" points="854.02,674.94 854.02,593.07 796.95,593.07 796.95,674.94 854.02,674.94"/>
<polygon class="store" data-id="80" tabindex="0" role="button" aria-label="Winkelvak 80" fill="#0097bc" points="856.75,593.07 856.75,654.40 876.26,654.40 876.26,593.07 856.75,593.07"/>
<polygon class="store" data-id="79" tabindex="0" role="button" aria-label="Winkelvak 79" fill="#0097bc" points="932.42,654.40 932.25,588.63 953.88,588.63 953.88,568.38 908.61,568.38 878.99,592.36 878.99,654.40 932.42,654.40"/>
<polygon class="store" data-id="78" tabindex="0" role="button" aria-label="Winkelvak 78" fill="#0097bc" points="953.88,478.89 938.10,467.09 909.49,467.09 909.49,565.60 953.88,565.60 953.88,478.89"/>
<polygon class="store" data-id="82" tabindex="0" role="button" aria-label="Winkelvak 82" fill="#0097bc" points="766.65,674.94 794.22,674.94 794.22,593.07 766.65,593.07 766.65,674.94"/>
<polygon class="store" data-id="85" tabindex="0" role="button" aria-label="Winkelvak 85" fill="#0097bc" points="645.05,593.07 645.05,593.17 592.22,593.17 592.22,674.94 709.11,674.94 709.11,593.07 645.05,593.07"/>
<polygon class="store" data-id="88" tabindex="0" role="button" aria-label="Winkelvak 88" fill="#0097bc" points="592.22,435.25 592.22,482.19 643.69,482.19 643.69,435.25 592.22,435.25"/>
<polygon class="store" data-id="87a" tabindex="0" role="button" aria-label="Winkelvak 87a" fill="#0097bc" points="592.22,484.96 592.22,533.07 643.69,533.07 643.69,484.96 592.22,484.96"/>
<polygon class="store" data-id="89" tabindex="0" role="button" aria-label="Winkelvak 89" fill="#0097bc" points="643.69,432.48 643.69,395.77 592.22,395.77 592.22,432.48 643.69,432.48"/>
<polygon class="store" data-id="90" tabindex="0" role="button" aria-label="Winkelvak 90" fill="#0097bc" points="643.69,345.47 592.22,345.47 592.22,393.00 643.69,393.00 643.69,345.47"/>
<polygon class="store" data-id="93" tabindex="0" role="button" aria-label="Winkelvak 93" fill="#0097bc" points="723.36,342.62 723.36,285.05 686.29,285.05 686.29,342.62 723.36,342.62"/>
<polygon class="store" data-id="94" tabindex="0" role="button" aria-label="Winkelvak 94" fill="#0097bc" points="762.97,342.62 762.97,285.05 726.09,285.05 726.09,342.62 762.97,342.62"/>
<polygon class="store" data-id="92" tabindex="0" role="button" aria-label="Winkelvak 92" fill="#0097bc" points="683.57,342.62 683.57,285.05 646.48,285.05 646.48,342.62 683.57,342.62"/>
<polygon class="store" data-id="68" tabindex="0" role="button" aria-label="Winkelvak 68" fill="#0097bc" points="1152.49,629.28 1122.21,629.28 1122.21,674.29 1152.49,674.29 1152.49,629.28"/>
<polygon class="store" data-id="69" tabindex="0" role="button" aria-label="Winkelvak 69" fill="#0097bc" points="1090.81,629.28 1090.81,674.29 1119.49,674.29 1119.49,629.28 1090.81,629.28"/>
<polygon class="store" data-id="67" tabindex="0" role="button" aria-label="Winkelvak 67" fill="#0097bc" points="1176.06,629.28 1155.21,629.28 1155.21,674.29 1176.06,674.29 1176.06,629.28"/>
<polygon class="store" data-id="66" tabindex="0" role="button" aria-label="Winkelvak 66" fill="#0097bc" points="1178.77,629.28 1178.77,674.29 1198.83,674.29 1198.83,629.28 1178.77,629.28"/>
<polygon class="store" data-id="71" tabindex="0" role="button" aria-label="Winkelvak 71" fill="#0097bc" points="1088.09,600.29 1086.43,600.29 1086.43,597.52 1088.09,597.52 1088.09,584.37 1026.37,584.37 1026.37,584.75 1055.34,584.75 1055.34,587.51 1026.37,587.51 1026.37,654.44 1041.71,654.44 1041.71,626.50 1088.09,626.50 1088.09,600.29"/>
<polygon class="store" data-id="70" tabindex="0" role="button" aria-label="Winkelvak 70" fill="#0097bc" points="1088.09,654.44 1088.09,629.28 1044.43,629.28 1044.43,654.44 1088.09,654.44"/>
<polygon class="store" data-id="72" tabindex="0" role="button" aria-label="Winkelvak 72" fill="#0097bc" points="1014.24,581.59 1058.64,581.59 1058.64,464.69 1027.57,464.69 1014.24,477.12 1014.24,581.59"/>
<polygon class="store" data-id="7" tabindex="0" role="button" aria-label="Winkelvak 7" fill="#0097bc" points="507.55,659.95 469.54,627.60 453.13,645.98 427.84,645.98 427.84,694.15 431.01,694.15 448.55,689.83 464.76,684.38 479.39,677.96 492.52,670.71 504.22,662.71 507.55,659.95"/>
<polygon class="store" data-id="2B" tabindex="0" role="button" aria-label="Winkelvak 2B" fill="#0097bc" points="273.43,628.81 327.51,628.81 327.51,602.47 273.43,602.47 273.43,628.81"/>
<polygon class="store" data-id="8" tabindex="0" role="button" aria-label="Winkelvak 8" fill="#0097bc" points="523.69,644.97 525.90,642.31 486.40,608.72 471.37,625.55 509.70,658.16 514.59,654.10 523.69,644.97"/>
<polygon class="store" data-id="10a" tabindex="0" role="button" aria-label="Winkelvak 10a" fill="#0097bc" points="505.09,538.75 505.09,486.43 417.74,486.43 417.74,538.75 505.09,538.75"/>
<polygon class="store" data-id="11-west" tabindex="0" role="button" aria-label="Winkelvak 11-west" fill="#0097bc" points="415.02,486.43 358.82,486.43 358.82,538.75 415.02,538.75 415.02,486.43"/>
<polygon class="store" data-id="brandweer" tabindex="0" role="button" aria-label="Winkelvak brandweer" fill="#0097bc" points="58.12,1239.36 200.51,1361.10 386.39,1361.10 386.39,1290.61 225.53,1290.61 99.86,1185.36 58.12,1239.36"/>
<polygon class="store" data-id="2A" tabindex="0" role="button" aria-label="Winkelvak 2A" fill="#0097bc" points="273.43,599.70 327.51,599.70 327.51,571.45 273.43,571.45 273.43,599.70"/>
<polygon class="store" data-id="1" tabindex="0" role="button" aria-label="Winkelvak 1" fill="#0097bc" points="273.43,568.68 327.51,568.68 327.51,523.19 273.43,523.19 273.43,568.68"/>
<polygon class="store" data-id="14" tabindex="0" role="button" aria-label="Winkelvak 14" fill="#0097bc" points="378.71,820.35 378.71,736.49 324.95,736.49 324.95,820.35 378.71,820.35"/>
<polygon class="store" data-id="15" tabindex="0" role="button" aria-label="Winkelvak 15" fill="#0097bc" points="273.05,736.49 273.05,820.35 322.23,820.35 322.23,736.49 273.05,736.49"/>
<polygon class="store" data-id="98" tabindex="0" role="button" aria-label="Winkelvak 98" fill="#0097bc" points="909.49,450.24 953.88,450.24 953.88,416.86 909.49,416.86 909.49,450.24"/>
<polygon class="store" data-id="97" tabindex="0" role="button" aria-label="Winkelvak 97" fill="#0097bc" points="953.88,414.09 953.88,373.15 909.49,373.15 909.49,414.09 953.88,414.09"/>
<polygon class="store" data-id="96" tabindex="0" role="button" aria-label="Winkelvak 96" fill="#0097bc" points="953.88,370.38 953.88,339.16 931.41,339.16 931.41,370.38 953.88,370.38"/>
<polygon class="store" data-id="95" tabindex="0" role="button" aria-label="Winkelvak 95" fill="#0097bc" points="928.69,285.88 825.88,285.88 825.88,370.38 928.69,370.38 928.69,285.88"/>
<polygon class="store" data-id="4" tabindex="0" role="button" aria-label="Winkelvak 4" fill="#0097bc" points="330.23,697.24 354.80,697.21 354.80,631.57 330.23,631.57 330.23,697.24"/>
<polygon class="store" data-id="3" tabindex="0" role="button" aria-label="Winkelvak 3" fill="#0097bc" points="273.04,631.57 273.05,697.30 327.51,697.24 327.51,631.57 273.04,631.57"/>
<polygon class="store" data-id="5" tabindex="0" role="button" aria-label="Winkelvak 5" fill="#0097bc" points="357.52,697.21 397.39,697.17 397.39,631.57 357.52,631.57 357.52,697.21"/>
<polygon class="store" data-id="6" tabindex="0" role="button" aria-label="Winkelvak 6" fill="#0097bc" points="425.11,697.14 425.11,631.57 400.12,631.57 400.12,697.17 425.11,697.14"/>
<polygon class="store" data-id="34" tabindex="0" role="button" aria-label="Winkelvak 34" fill="#0097bc" points="910.30,1024.13 910.30,906.76 856.01,906.76 856.01,1024.13 910.30,1024.13"/>
<polygon class="store" data-id="35" tabindex="0" role="button" aria-label="Winkelvak 35" fill="#0097bc" points="783.16,1024.13 853.29,1024.13 853.29,954.67 783.16,954.67 783.16,1024.13"/>
<polygon class="store" data-id="36" tabindex="0" role="button" aria-label="Winkelvak 36" fill="#0097bc" points="709.05,1024.13 780.43,1024.13 780.43,954.67 709.05,954.67 709.05,1024.13"/>
<polygon class="store" data-id="25" tabindex="0" role="button" aria-label="Winkelvak 25" fill="#0097bc" points="672.47,790.94 672.47,738.76 616.18,738.76 616.18,790.70 658.70,790.70 658.70,790.94 672.47,790.94"/>
<polygon class="store" data-id="26" tabindex="0" role="button" aria-label="Winkelvak 26" fill="#0097bc" points="675.19,726.28 675.19,790.94 705.54,790.94 705.54,726.28 675.19,726.28"/>
<polygon class="store" data-id="27" tabindex="0" role="button" aria-label="Winkelvak 27" fill="#0097bc" points="742.99,726.28 708.26,726.28 708.26,790.94 742.99,790.94 742.99,726.28"/>
<polygon class="store" data-id="28" tabindex="0" role="button" aria-label="Winkelvak 28" fill="#0097bc" points="780.43,726.28 745.71,726.28 745.71,790.94 780.43,790.94 780.43,726.28"/>
<polygon class="store" data-id="29" tabindex="0" role="button" aria-label="Winkelvak 29" fill="#0097bc" points="817.88,726.28 783.16,726.28 783.16,790.94 817.88,790.94 817.88,726.28"/>
<polygon class="store" data-id="30" tabindex="0" role="button" aria-label="Winkelvak 30" fill="#0097bc" points="853.29,726.28 820.60,726.28 820.60,790.94 853.29,790.94 853.29,726.28"/>
<polygon class="store" data-id="31" tabindex="0" role="button" aria-label="Winkelvak 31" fill="#0097bc" points="910.30,726.28 856.01,726.28 856.01,790.94 910.30,790.94 910.30,726.28"/>
<polygon class="store" data-id="24" tabindex="0" role="button" aria-label="Winkelvak 24" fill="#0097bc" points="614.82,793.18 603.93,793.18 603.93,822.01 656.75,822.01 656.75,793.47 614.87,793.47 614.82,793.18"/>
<polygon class="store" data-id="21" tabindex="0" role="button" aria-label="Winkelvak 21" fill="#0097bc" points="603.93,886.35 603.93,914.36 656.75,914.36 656.75,886.35 603.93,886.35"/>
<polygon class="store" data-id="19" tabindex="0" role="button" aria-label="Winkelvak 19" fill="#0097bc" points="656.75,973.95 656.75,947.91 603.93,947.91 603.93,973.95 656.75,973.95"/>
<polygon class="store" data-id="20" tabindex="0" role="button" aria-label="Winkelvak 20" fill="#0097bc" points="656.75,945.14 656.75,917.13 603.93,917.13 603.93,945.14 656.75,945.14"/>
<polygon class="store" data-id="22" tabindex="0" role="button" aria-label="Winkelvak 22" fill="#0097bc" points="603.93,855.57 603.93,883.58 656.75,883.58 656.75,855.57 603.93,855.57"/>
<polygon class="store" data-id="23" tabindex="0" role="button" aria-label="Winkelvak 23" fill="#0097bc" points="656.75,852.80 656.75,824.78 603.93,824.78 603.93,852.80 656.75,852.80"/>
<polygon class="store" data-id="32" tabindex="0" role="button" aria-label="Winkelvak 32" fill="#0097bc" points="910.30,793.71 856.01,793.71 856.01,838.26 910.30,838.26 910.30,793.71"/>
<polygon class="store" data-id="33" tabindex="0" role="button" aria-label="Winkelvak 33" fill="#0097bc" points="856.01,841.03 856.01,903.99 910.30,903.99 910.30,841.03 856.01,841.03"/>
<polygon class="store" data-id="55" tabindex="0" role="button" aria-label="Winkelvak 55" fill="#0097bc" points="1461.75,888.88 1460.57,885.76 1459.19,882.74 1457.64,879.83 1455.91,877.04 1454.01,874.37 1451.96,871.83 1449.75,869.43 1447.39,867.18 1444.90,865.09 1442.28,863.16 1439.54,861.40 1436.68,859.82 1433.72,858.42 1430.66,857.22 1427.50,856.22 1424.26,855.42 1420.95,854.85 1417.56,854.50 1414.12,854.38 1410.67,854.50 1407.29,854.85 1403.97,855.42 1400.74,856.22 1397.58,857.22 1394.52,858.42 1391.56,859.82 1388.70,861.40 1385.96,863.16 1383.34,865.09 1380.85,867.18 1378.50,869.43 1376.29,871.83 1374.24,874.37 1372.34,877.04 1370.61,879.83 1369.06,882.74 1367.69,885.76 1366.51,888.88 1365.52,892.09 1364.75,895.39 1364.18,898.77 1363.84,902.21 1363.75,907.44 1364.15,912.51 1365.03,917.46 1365.88,920.69 1366.93,923.84 1368.88,928.43 1371.27,932.80 1374.09,936.94 1377.32,940.82 1380.94,944.38 1384.83,947.53 1388.97,950.26 1393.33,952.54 1397.89,954.38 1402.61,955.75 1407.47,956.65 1412.44,957.06 1417.47,956.98 1422.41,956.40 1427.23,955.34 1431.90,953.82 1436.40,951.83 1440.69,949.40 1444.75,946.53 1448.55,943.24 1452.05,939.55 1455.15,935.59 1457.82,931.37 1460.07,926.92 1461.87,922.28 1463.22,917.46 1464.10,912.51 1464.51,907.44 1464.42,902.21 1464.07,898.77 1463.51,895.39 1462.73,892.09 1461.75,888.88"/>
<polygon class="store" data-id="110" tabindex="0" role="button" aria-label="Winkelvak 110" fill="#0097bc" points="1206.61,1163.77 1228.40,1181.17 1228.40,1244.92 1243.38,1244.92 1243.38,1286.54 1318.25,1286.54 1318.25,1184.83 1393.68,1159.02 1408.71,1250.76 1507.91,1228.31 1480.43,1054.36 1458.77,1042.93 1349.14,1078.38 1360.68,1103.85 1332.01,1115.74 1317.65,1084.25 1206.61,1084.25 1206.61,1163.77"/>
<polygon class="store" data-id="57" tabindex="0" role="button" aria-label="Winkelvak 57" fill="#0097bc" points="1560.06,1056.71 1591.17,1047.38 1577.56,986.96 1546.36,995.74 1560.06,1056.71"/>
<polygon class="store" data-id="56" tabindex="0" role="button" aria-label="Winkelvak 56" fill="#0097bc" points="1560.66,1059.41 1569.06,1096.78 1651.56,1070.63 1642.65,1034.84 1560.66,1059.41"/>
<polygon class="store" data-id="60" tabindex="0" role="button" aria-label="Winkelvak 60" fill="#0097bc" points="1757.70,1036.98 1752.96,877.76 1708.17,892.87 1677.65,795.95 1545.53,832.29 1593.79,1046.60 1643.60,1031.66 1644.01,1033.07 1644.97,1032.83 1654.17,1069.80 1757.70,1036.98"/>
<polygon class="store" data-id="58" tabindex="0" role="button" aria-label="Winkelvak 58" fill="#0097bc" points="1566.52,937.93 1535.37,946.84 1545.75,993.03 1576.95,984.26 1566.52,937.93"/>
<polygon class="store" data-id="59" tabindex="0" role="button" aria-label="Winkelvak 59" fill="#0097bc" points="1553.37,879.55 1522.25,888.44 1534.77,944.14 1565.91,935.22 1553.37,879.55"/>
<polygon class="store" data-id="61" tabindex="0" role="button" aria-label="Winkelvak 61" fill="#0097bc" points="1497.63,782.26 1502.65,814.71 1582.15,791.29 1582.15,759.64 1497.63,782.26"/>
<polygon class="store" data-id="62" tabindex="0" role="button" aria-label="Winkelvak 62" fill="#0097bc" points="1491.17,740.43 1497.21,779.50 1582.15,756.78 1582.15,740.43 1491.17,740.43"/>
<polygon class="store" data-id="63" tabindex="0" role="button" aria-label="Winkelvak 63" fill="#0097bc" points="1378.72,604.73 1378.72,737.66 1582.15,737.66 1582.15,604.73 1378.72,604.73"/>
<polygon class="store" data-id="64" tabindex="0" role="button" aria-label="Winkelvak 64" fill="#0097bc" points="1299.19,553.75 1255.62,553.93 1255.62,670.22 1312.01,670.22 1312.01,598.35 1346.85,598.35 1346.85,494.29 1395.61,494.29 1395.61,384.42 1319.55,384.42 1319.55,516.76 1299.19,516.76 1299.19,553.75"/>
<polygon class="store" data-id="74a" tabindex="0" role="button" aria-label="Winkelvak 74a" fill="#0097bc" points="1055.58,395.45 1055.58,373.84 1011.21,373.84 1011.21,395.45 1055.58,395.45"/>
<polygon class="store" data-id="74" tabindex="0" role="button" aria-label="Winkelvak 74" fill="#0097bc" points="1055.58,419.63 1055.58,398.22 1011.21,398.22 1011.21,419.63 1055.58,419.63"/>
<polygon class="store" data-id="73" tabindex="0" role="button" aria-label="Winkelvak 73" fill="#0097bc" points="1055.58,450.24 1055.58,422.40 1011.21,422.40 1011.21,450.24 1055.58,450.24"/>
<polygon class="store" data-id="75" tabindex="0" role="button" aria-label="Winkelvak 75" fill="#0097bc" points="1011.21,371.07 1104.50,371.07 1104.50,304.48 1011.21,304.48 1011.21,371.07"/>
<polygon class="store" data-id="76" tabindex="0" role="button" aria-label="Winkelvak 76" fill="#0097bc" points="1171.18,285.88 1107.33,285.88 1107.33,304.48 1107.18,304.48 1107.18,371.07 1171.18,371.07 1171.18,285.88"/>
<polygon class="store" data-id="77" tabindex="0" role="button" aria-label="Winkelvak 77" fill="#0097bc" points="1218.05,285.88 1173.90,285.88 1173.90,371.07 1218.05,371.07 1218.05,285.88"/>
<polygon class="store" data-id="42" tabindex="0" role="button" aria-label="Winkelvak 42" fill="#0097bc" points="1072.36,820.54 1072.35,727.09 1028.78,727.11 1028.78,820.54 1072.36,820.54"/>
<polygon class="store" data-id="40" tabindex="0" role="button" aria-label="Winkelvak 40" fill="#0097bc" points="1072.36,871.13 1072.36,847.53 1028.78,847.53 1028.78,871.13 1072.36,871.13"/>
<polygon class="store" data-id="39" tabindex="0" role="button" aria-label="Winkelvak 39" fill="#0097bc" points="1072.36,899.74 1072.36,873.90 1028.78,873.90 1028.78,899.74 1072.36,899.74"/>
<polygon class="store" data-id="38" tabindex="0" role="button" aria-label="Winkelvak 38" fill="#0097bc" points="1072.36,945.50 1072.36,902.51 1028.78,902.51 1028.78,945.50 1072.36,945.50"/>
<polygon class="store" data-id="41" tabindex="0" role="button" aria-label="Winkelvak 41" fill="#0097bc" points="1072.36,844.76 1072.36,823.30 1028.78,823.30 1028.78,844.76 1072.36,844.76"/>
<polygon class="store" data-id="46" tabindex="0" role="button" aria-label="Winkelvak 46" fill="#0097bc" points="1197.84,727.03 1169.19,727.04 1169.19,805.37 1197.84,805.37 1197.84,727.03"/>
<polygon class="store" data-id="49" tabindex="0" role="button" aria-label="Winkelvak 49" fill="#0097bc" points="1310.37,726.97 1252.26,727.00 1252.26,806.75 1252.09,806.75 1252.09,812.65 1310.37,812.65 1310.37,726.97"/>
<polygon class="store" data-id="51" tabindex="0" role="button" aria-label="Winkelvak 51" fill="#0097bc" points="1252.09,847.91 1252.09,877.63 1310.37,877.63 1310.37,847.91 1252.09,847.91"/>
<polygon class="store" data-id="53" tabindex="0" role="button" aria-label="Winkelvak 53" fill="#0097bc" points="1252.09,912.89 1252.09,942.61 1310.37,942.61 1310.37,912.89 1252.09,912.89"/>
<polygon class="store" data-id="54" tabindex="0" role="button" aria-label="Winkelvak 54" fill="#0097bc" points="1252.09,945.38 1252.09,1021.82 1310.37,1021.82 1310.37,945.38 1252.09,945.38"/>
<polygon class="store" data-id="52" tabindex="0" role="button" aria-label="Winkelvak 52" fill="#0097bc" points="1252.09,880.40 1252.09,910.12 1310.37,910.12 1310.37,880.40 1252.09,880.40"/>
<polygon class="store" data-id="50" tabindex="0" role="button" aria-label="Winkelvak 50" fill="#0097bc" points="1252.09,815.42 1252.09,845.14 1310.37,845.14 1310.37,815.42 1252.09,815.42"/>
<polygon class="store" data-id="48" tabindex="0" role="button" aria-label="Winkelvak 48" fill="#0097bc" points="1228.60,805.37 1249.54,805.37 1249.54,727.00 1228.60,727.01 1228.60,805.37"/>
<polygon class="store" data-id="47" tabindex="0" role="button" aria-label="Winkelvak 47" fill="#0097bc" points="1225.89,727.01 1200.57,727.02 1200.57,805.37 1225.89,805.37 1225.89,727.01"/>
<polygon class="store" data-id="45" tabindex="0" role="button" aria-label="Winkelvak 45" fill="#0097bc" points="1166.47,727.04 1137.83,727.06 1137.83,805.37 1166.47,805.37 1166.47,727.04"/>
<polygon class="store" data-id="44" tabindex="0" role="button" aria-label="Winkelvak 44" fill="#0097bc" points="1106.45,805.37 1135.15,805.37 1135.15,727.06 1106.45,727.07 1106.45,805.37"/>
<polygon class="store" data-id="43" tabindex="0" role="button" aria-label="Winkelvak 43" fill="#0097bc" points="1103.73,727.07 1075.07,727.09 1075.07,805.37 1103.73,805.37 1103.73,727.07"/>
<polygon class="store" data-id="65" tabindex="0" role="button" aria-label="Winkelvak 65" fill="#0097bc" points="1201.55,674.29 1231.96,674.29 1231.96,629.28 1201.55,629.28 1201.55,674.29"/>
<polygon class="store" data-id="10" tabindex="0" role="button" aria-label="Winkelvak 10" fill="#0097bc" points="562.06,558.03 562.95,549.87 563.53,540.14 560.81,540.14 560.81,486.43 507.82,486.43 507.82,540.14 510.94,540.14 510.94,561.97 561.46,561.97 562.06,558.03"/>
<polygon class="store" data-id="9" tabindex="0" role="button" aria-label="Winkelvak 9" fill="#0097bc" points="510.94,564.75 510.94,581.26 488.23,606.67 527.66,640.20 531.61,635.44 538.43,625.62 544.23,615.62 549.09,605.55 553.09,595.52 556.30,585.65 558.82,576.03 561.03,564.75 510.94,564.75"/>
<polygon class="store" data-id="12" tabindex="0" role="button" aria-label="Winkelvak 12" fill="#0097bc" points="434.58,779.30 426.01,775.27 418.46,771.31 411.89,767.49 403.17,761.71 395.91,756.08 390.20,750.88 385.86,746.24 383.64,743.52 381.43,740.41 381.43,820.35 439.63,820.35 454.96,787.80 457.42,789.00 442.65,820.35 461.47,820.35 461.47,789.60 449.50,785.39 444.24,783.37 434.58,779.30"/>
<polygon class="store" data-id="13" tabindex="0" role="button" aria-label="Winkelvak 13" fill="#0097bc" points="273.05,905.35 417.11,1002.04 417.11,974.09 555.73,974.09 555.73,823.14 273.05,823.14 273.05,905.35"/>
<polygon class="store" data-id="37a" tabindex="0" role="button" aria-label="Winkelvak 37a" fill="#0097bc" points="1057.71,948.28 1057.71,1024.59 1141.09,1024.59 1141.09,948.28 1057.71,948.28"/>
<polygon class="store" data-id="37b" tabindex="0" role="button" aria-label="Winkelvak 37b" fill="#0097bc" points="1221.30,948.28 1142.62,948.28 1142.62,1024.59 1221.30,1024.59 1221.30,948.28"/>
<polygon class="store" data-id="112" tabindex="0" role="button" aria-label="Winkelvak 112" fill="#0097bc" points="648.35,1069.39 648.35,1148.34 602.58,1148.34 602.58,1069.39 648.35,1069.39"/>
<polygon class="store" data-id="111" tabindex="0" role="button" aria-label="Winkelvak 111" fill="#0097bc" points="888.51,1126.72 810.90,1126.72 810.90,1070.48 652.27,1070.48 652.27,1152.52 603.66,1152.52 603.66,1297.10 804.09,1297.10 804.09,1281.28 833.78,1281.28 833.78,1298.19 888.51,1298.19 888.51,1126.72"/>
<polygon class="store" data-id="113" tabindex="0" role="button" aria-label="Winkelvak 113" fill="#0097bc" points="1664.14,434.78 1664.14,499.65 1587.52,499.65 1587.52,434.78 1664.14,434.78"/>
<g id="numbers"></g></svg>
      <div class="controls"><button data-zoom="in" aria-label="Inzoomen">+</button><button data-zoom="out"
          aria-label="Uitzoomen">&minus;</button><button data-zoom="reset" aria-label="Reset">&#8634;</button></div>
      <div class="tip" hidden></div>
      <div class="sheet" role="dialog" aria-live="polite"><button class="close" aria-label="Sluiten">&times;</button>
        <h2></h2>
        <p></p>
      </div>
    </div>
    <aside>
      <div class="filters">
        <input type="search" placeholder="Zoek op winkel of nummer" aria-label="Zoek winkel">
        <label><input type="checkbox" id="onlyData"> Alleen winkels met gegevens</label>
      </div>
      <div class="count"></div>
      <ul></ul>
    </aside>
  </div>
  <script>
    // Winkeldata, key = data-id van de polygon. Aanvullen of vervangen door een fetch naar je API.
    const stores = <?php
      $mapStores = [];
      foreach (config('map-shops') as [$number, $name]) {
          $ids = match ((string) $number) {
              '2' => $name === 'SNS' ? ['2A'] : ['2B'],
              '87' => ['87a', '87b'],
              '27&28' => ['27', '28'],
              default => [(string) $number],
          };
          foreach ($ids as $id) $mapStores[$id] = ['name' => $name, 'info' => 'Winkel '.$number];
      }
      echo json_encode($mapStores, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?>;
    const svg = document.getElementById('map');
    const wrap = svg.parentElement;
    const viewer = document.querySelector('.viewer');
    const viewerClose = document.querySelector('.viewer-close');
    const viewerOpen = document.querySelector('.viewer-open');
    const tip = document.querySelector('.tip');
    const sheet = document.querySelector('.sheet');
    const storeEls = [...svg.querySelectorAll('.store')];
    const byId = Object.fromEntries(storeEls.map(el => [el.dataset.id, el]));
    const info = id => stores[id] || { name: byId[id].getAttribute('aria-label'), info: 'Nog geen gegevens' };
    const mobileView = matchMedia('(max-width: 1000px)');

    function isCollapsed() {
      return mobileView.matches && viewer.classList.contains('is-collapsed');
    }

    function openViewer() {
      viewer.classList.remove('is-collapsed');
    }

    function closeViewer() {
      if (!mobileView.matches) return;
      select(null);
      viewer.classList.add('is-collapsed');
    }

    function showTip(el, clientX, clientY) {
      if (isCollapsed()) return;
      const s = info(el.dataset.id);
      tip.innerHTML = `<h3>${s.name}</h3><p>${s.info}</p>`;
      tip.hidden = false;
      const m = wrap.getBoundingClientRect();
      let x = clientX - m.left + 14, y = clientY - m.top + 14;
      const w = tip.offsetWidth, h = tip.offsetHeight;
      if (x + w > m.width - 4) x = clientX - m.left - w - 14;   // flip naar links
      if (y + h > m.height - 4) y = clientY - m.top - h - 14;   // flip naar boven
      tip.style.left = Math.max(4, x) + 'px';
      tip.style.top = Math.max(4, y) + 'px';
    }
    function hideTip() { tip.hidden = true; }

    let active = null;
    const hl = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
    hl.setAttribute('class', 'hl'); hl.style.display = 'none';
    svg.querySelector('#numbers').before(hl);   // boven de winkels, onder de nummers
    function select(id) {
      if (isCollapsed()) openViewer();
      if (active) active.classList.remove('active');
      active = id ? byId[id] : null;
      if (!active) { sheet.classList.remove('open'); hl.style.display = 'none'; document.dispatchEvent(new CustomEvent('storeselect', { detail: null })); return; }
      active.classList.add('active');
      hl.setAttribute('points', active.getAttribute('points')); hl.style.display = '';
      const s = info(id);
      sheet.querySelector('h2').textContent = s.name;
      sheet.querySelector('p').textContent = s.info;
      sheet.classList.add('open');
      hideTip();
      document.dispatchEvent(new CustomEvent('storeselect', { detail: id }));
    }
    sheet.querySelector('.close').addEventListener('click', () => select(null));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') select(null); });
    viewerClose.addEventListener('click', closeViewer);
    viewerOpen.addEventListener('click', openViewer);
    viewer.addEventListener('click', e => {
      if (isCollapsed() && !e.target.closest('.viewer-close')) openViewer();
    });

    // Hover-tooltip alleen voor een echte muis; touch krijgt de sheet via tap
    storeEls.forEach(el => {
      el.addEventListener('pointermove', e => { if (e.pointerType === 'mouse') showTip(el, e.clientX, e.clientY); });
      el.addEventListener('pointerleave', hideTip);
      el.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); select(el.dataset.id); } });
    });

    // ---- Pan & zoom via de viewBox (geen library nodig) ----
    const [, , W, H] = svg.getAttribute('viewBox').split(' ').map(Number);
    const MIN_W = W / 10;          // maximale zoom: 10x
    let vb = { x: 0, y: 0, w: W, h: H };

    function aspect() { const r = svg.getBoundingClientRect(); return r.height / r.width; }
    function fit() {                 // hele kaart in beeld; op staande schermen vullen we de hoogte
      const a = aspect();
      let w = W, h = W * a;
      if (h < H) { h = H; w = H / a; }
      else if (a > 1) { h = H; w = H / a; }   // portrait: kaart vult de hoogte, zijwaarts pannen
      vb = { x: (W - w) / 2, y: (H - h) / 2, w, h };
      apply();
    }
    function clamp() {
      const a = aspect();
      vb.w = Math.min(Math.max(vb.w, MIN_W), Math.max(W, H / a));
      vb.h = vb.w * a;
      // binnen de kaart blijven; als de view groter is dan de kaart, centreren
      vb.x = vb.w >= W ? (W - vb.w) / 2 : Math.min(Math.max(vb.x, 0), W - vb.w);
      vb.y = vb.h >= H ? (H - vb.h) / 2 : Math.min(Math.max(vb.y, 0), H - vb.h);
    }
    function apply() { clamp(); svg.setAttribute('viewBox', `${vb.x} ${vb.y} ${vb.w} ${vb.h}`); }
    function toSvg(cx, cy) {
      const r = svg.getBoundingClientRect();
      return { x: vb.x + (cx - r.left) / r.width * vb.w, y: vb.y + (cy - r.top) / r.height * vb.h };
    }
    function zoomAt(factor, cx, cy) {
      const p = toSvg(cx, cy);
      const nw = Math.min(Math.max(vb.w * factor, MIN_W), W * 2);
      const k = nw / vb.w;
      vb = { x: p.x - (p.x - vb.x) * k, y: p.y - (p.y - vb.y) * k, w: nw, h: nw * aspect() };
      apply();
    }
    function zoomCenter(factor) {
      const r = svg.getBoundingClientRect();
      zoomAt(factor, r.left + r.width / 2, r.top + r.height / 2);
    }

    svg.addEventListener('wheel', e => {
      e.preventDefault();
      zoomAt(Math.exp(e.deltaY * 0.0015), e.clientX, e.clientY);
    }, { passive: false });

    // Pointer events: 1 vinger/muis = pannen, 2 vingers = pinch; korte tik zonder beweging = selecteren
    const pointers = new Map();
    let start = null, pinch = null;
    svg.addEventListener('pointerdown', e => {
      svg.setPointerCapture(e.pointerId);
      pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
      if (pointers.size === 1) {
        start = { x: e.clientX, y: e.clientY, vb: { ...vb }, target: e.target.closest('.store'), moved: false };
      } else if (pointers.size === 2) {
        const [a, b] = [...pointers.values()];
        pinch = { d: Math.hypot(a.x - b.x, a.y - b.y), mid: { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 } };
        if (start) start.moved = true;
      }
    });
    svg.addEventListener('pointermove', e => {
      if (!pointers.has(e.pointerId)) return;
      pointers.set(e.pointerId, { x: e.clientX, y: e.clientY });
      if (pointers.size === 2 && pinch) {
        const [a, b] = [...pointers.values()];
        const d = Math.hypot(a.x - b.x, a.y - b.y);
        const mid = { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2 };
        const r = svg.getBoundingClientRect();
        vb.x -= (mid.x - pinch.mid.x) / r.width * vb.w;   // pan met het midden mee
        vb.y -= (mid.y - pinch.mid.y) / r.height * vb.h;
        zoomAt(pinch.d / d, mid.x, mid.y);
        pinch = { d, mid };
      } else if (pointers.size === 1 && start) {
        const dx = e.clientX - start.x, dy = e.clientY - start.y;
        if (!start.moved && Math.hypot(dx, dy) > 6) { start.moved = true; svg.classList.add('dragging'); hideTip(); }
        if (start.moved) {
          const r = svg.getBoundingClientRect();
          vb.x = start.vb.x - dx / r.width * vb.w;
          vb.y = start.vb.y - dy / r.height * vb.h;
          apply();
        }
      }
    });
    function endPointer(e) {
      if (!pointers.has(e.pointerId)) return;
      pointers.delete(e.pointerId);
      if (pointers.size < 2) pinch = null;
      if (pointers.size === 0) {
        if (start && !start.moved) select(start.target ? start.target.dataset.id : null);
        start = null;
        svg.classList.remove('dragging');
      } else if (pointers.size === 1) {        // van pinch terug naar pannen zonder sprong
        const [p] = [...pointers.values()];
        start = { x: p.x, y: p.y, vb: { ...vb }, target: null, moved: true };
      }
    }
    svg.addEventListener('pointerup', endPointer);
    svg.addEventListener('pointercancel', endPointer);

    document.querySelector('.controls').addEventListener('click', e => {
      const z = e.target.closest('button')?.dataset.zoom;
      if (z === 'in') zoomCenter(0.7);
      if (z === 'out') zoomCenter(1 / 0.7);
      if (z === 'reset') fit();
    });

    // Toetsenbord: bij focus op een winkel die buiten beeld valt, erheen pannen
    storeEls.forEach(el => el.addEventListener('focus', () => {
      const b = el.getBBox();
      if (b.x < vb.x || b.y < vb.y || b.x + b.width > vb.x + vb.w || b.y + b.height > vb.y + vb.h) {
        vb.x = b.x + b.width / 2 - vb.w / 2; vb.y = b.y + b.height / 2 - vb.h / 2; apply();
      }
    }));

    new ResizeObserver(() => {
      const cx = vb.x + vb.w / 2, cy = vb.y + vb.h / 2; vb.h = vb.w * aspect();
      vb.x = cx - vb.w / 2; vb.y = cy - vb.h / 2; apply();
    }).observe(svg);
    fit();

    // ---- Soepel naar een winkel zoomen ----
    const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    let anim = null;
    function animateTo(target, ms = 450) {
      cancelAnimationFrame(anim);
      if (reduceMotion) { vb = { ...target }; apply(); return; }
      const from = { ...vb }, t0 = performance.now();
      const ease = t => t < .5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
      const step = now => {
        const t = Math.min(1, (now - t0) / ms), k = ease(t);
        vb = {
          x: from.x + (target.x - from.x) * k, y: from.y + (target.y - from.y) * k,
          w: from.w + (target.w - from.w) * k, h: 0
        };
        vb.h = vb.w * aspect(); apply();
        if (t < 1) anim = requestAnimationFrame(step);
      };
      anim = requestAnimationFrame(step);
    }
    function focusStore(id) {
      const b = byId[id].getBBox(), a = aspect();
      // zo ver inzoomen dat de unit ongeveer 1/5 van de breedte of hoogte vult
      let w = Math.max(b.width * 5, b.height * 5 / a);
      w = Math.min(Math.max(w, W / 8), W / 2);
      const h = w * a;
      animateTo({ x: b.x + b.width / 2 - w / 2, y: b.y + b.height / 2 - h / 2, w, h });
    }
    // Handmatig pannen of zoomen breekt een lopende animatie af
    svg.addEventListener('pointerdown', () => cancelAnimationFrame(anim));
    svg.addEventListener('wheel', () => cancelAnimationFrame(anim), { passive: true });

    // ---- Winkellijst ----
    const list = document.querySelector('aside ul');
    const search = document.querySelector('aside input[type=search]');
    const onlyData = document.getElementById('onlyData');
    const count = document.querySelector('.count');
    const ids = storeEls.map(el => el.dataset.id)
      .filter((v, i, a) => a.indexOf(v) === i)
      .sort((a, b) => a.localeCompare(b, 'nl', { numeric: true }));

    function render() {
      const q = search.value.trim().toLowerCase();
      list.innerHTML = '';
      let n = 0;
      ids.forEach(id => {
        if (onlyData.checked && !stores[id]) return;
        const s = info(id);
        if (q && !(id.replace('-2', '').toLowerCase().includes(q) || s.name.toLowerCase().includes(q))) return;
        const li = document.createElement('li');
        li.innerHTML = `<button data-id="${id}"><span class="sw" style="background:${byId[id].getAttribute('fill')}"></span>
      <span class="n">${id.replace('-2', '')}</span><span>${s.name}<br><small>${s.info}</small></span></button>`;
        if (active && active.dataset.id === id) li.firstChild.classList.add('active');
        list.appendChild(li); n++;
      });
      count.textContent = `${n} van ${ids.length} units`;
      if (!n) list.innerHTML = '<li class="empty">Niets gevonden.</li>';
    }
    search.addEventListener('input', render);
    onlyData.addEventListener('change', render);

    list.addEventListener('click', e => {
      const b = e.target.closest('button'); if (!b) return;
      const id = b.dataset.id;
      openViewer();
      if (window.innerWidth <= 1000) document.querySelector('.viewer').scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
      focusStore(id);
      select(id);
    });
    // Hover in de lijst licht de unit op de kaart op
    list.addEventListener('pointerover', e => { const b = e.target.closest('button'); if (b) byId[b.dataset.id].classList.add('active'); });
    list.addEventListener('pointerout', e => {
      const b = e.target.closest('button');
      if (b && byId[b.dataset.id] !== active) byId[b.dataset.id].classList.remove('active');
    });

    // Selectie op de kaart: markeer in de lijst en scroll hem in beeld (alleen binnen de lijst)
    document.addEventListener('storeselect', e => {
      let hit = null;
      list.querySelectorAll('button').forEach(b => { const on = b.dataset.id === e.detail; b.classList.toggle('active', on); if (on) hit = b; });
      if (hit) {
        const lr = list.getBoundingClientRect(), br = hit.getBoundingClientRect();
        if (br.top < lr.top || br.bottom > lr.bottom) list.scrollTop += br.top - lr.top - lr.height / 2 + br.height / 2;
      }
    });
    sheet.querySelector('.close').addEventListener('click', () => list.querySelectorAll('button.active').forEach(b => b.classList.remove('active')));
    render();
  </script>
</body>

</html>