add_shortcode('dj_certificate', function($atts){
  $atts = shortcode_atts([
    'product_id' => 0,
    'store_name' => 'Darwish Gems & Jewellery',
    'accent'     => '#d6b37a'
  ], $atts);

  $pid = dj_get_product_id($atts['product_id']);
  if (!$pid) return '';

  $report = dj_get_meta($pid, '_dj_report_no', '');
  if (!$report) $report = 'PENDING';

  $date = dj_get_meta($pid, '_dj_cert_date', wp_date('d/m/Y'));

  // ✅ Your working meta keys (from WCFM custom fields)
  $weight       = dj_clean_text(dj_get_wcfm_value($pid, ['dj_weight','_dj_weight','weight','Weight'], ''));
  $shape        = dj_clean_text(dj_get_wcfm_value($pid, ['dj_shape','_dj_shape','shape','Shape'], ''));
  $color        = dj_clean_text(dj_get_wcfm_value($pid, ['dj_color','_dj_color','color','Color'], ''));
  $origin       = dj_clean_text(dj_get_wcfm_value($pid, ['dj_origin','_dj_origin','origin','Origin'], ''));
  $treatment    = dj_clean_text(dj_get_wcfm_value($pid, ['dj_treatment','_dj_treatment','treatment','Treatment'], ''));
  $type         = dj_clean_text(dj_get_wcfm_value($pid, ['dj_gem_type','_dj_gem_type','gem_type','Gem Type','GemType'], ''));
  $transparency = dj_clean_text(dj_get_wcfm_value($pid, ['dj_transparency','_dj_transparency','transparency','Transparency'], ''));
  $dimensions   = dj_clean_text(dj_get_wcfm_value($pid, ['dj_dimensions','_dj_dimensions','dimensions','Dimensions','Dimensions (mm)'], ''));
  $comments     = dj_clean_text(dj_get_wcfm_value($pid, ['dj_comments','_dj_comments','comments','Comments'], ''));

  $img = get_the_post_thumbnail_url($pid, 'medium');
  if (!$img) $img = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('medium') : '';

  $verify_url = dj_certificate_verify_url($report, $pid);
  $qr = 'https://api.qrserver.com/v1/create-qr-code/?size=210x210&data=' . rawurlencode($verify_url);

  // helper
  $val = function($v){
    $v = trim((string)$v);
    return $v !== '' ? $v : '—';
  };

  ob_start(); ?>
  <style>
    .dj-cert2{
      --gold: <?php echo esc_attr($atts['accent']); ?>;
      --ink:#111827;
      --muted:#6b7280;
      --line:#e5e7eb;
      --paper:#ffffff;
      --bg:#0b0b0c;

      max-width:920px;
      margin:18px auto;
      font-family: Arial, sans-serif;
      border-radius:22px;
      overflow:hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,.12);
      border:1px solid rgba(0,0,0,.08);
      background: var(--paper);
    }

    /* Header */
    .dj-cert2-h{
      background: radial-gradient(1200px 220px at 20% 0%, rgba(214,179,122,.35), transparent 55%),
                  linear-gradient(90deg, #0b0b0c, #111213);
      padding:18px 22px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:14px;
    }
    .dj-cert2-brand{
      display:flex;
      flex-direction:column;
      gap:4px;
    }
    .dj-cert2-brand .name{
      color:var(--gold);
      font-weight:900;
      letter-spacing:1px;
      font-size:18px;
      text-transform:uppercase;
      line-height:1.1;
    }
    .dj-cert2-brand .sub{
      color:#c7cbd1;
      font-weight:700;
      font-size:12px;
      letter-spacing:.4px;
    }
    .dj-cert2-badges{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      justify-content:flex-end;
      text-align:right;
    }
    .dj-chip{
      border:1px solid rgba(214,179,122,.45);
      color:#e9e3d6;
      font-size:11px;
      font-weight:800;
      padding:8px 10px;
      border-radius:999px;
      background:rgba(214,179,122,.10);
      backdrop-filter: blur(2px);
      letter-spacing:.3px;
    }

    /* Body grid */
    .dj-cert2-b{
      display:grid;
      grid-template-columns: 210px 1fr 210px;
      gap:16px;
      padding:18px;
      background: linear-gradient(180deg, #ffffff, #fcfcfc);
    }

    /* Image */
    .dj-cert2-img{
      border:1px solid var(--line);
      border-radius:18px;
      padding:12px;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#fff;
    }
    .dj-cert2-img img{
      max-width:170px;
      height:auto;
      border-radius:14px;
      display:block;
    }

    /* QR */
    .dj-cert2-qr{
      border:1px solid var(--line);
      border-radius:18px;
      padding:12px;
      background:#fff;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:flex-start;
      gap:10px;
    }
    .dj-cert2-qr img{
      width:160px;
      height:160px;
      border-radius:14px;
      border:1px solid var(--line);
      display:block;
    }
    .dj-cert2-qr small{
      color:var(--muted);
      font-weight:700;
      font-size:11px;
      text-align:center;
      line-height:1.25;
    }

    /* Details */
    .dj-cert2-card{
      border:1px solid var(--line);
      border-radius:18px;
      background:#fff;
      overflow:hidden;
    }
    .dj-cert2-card .topline{
      padding:12px 14px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      background: linear-gradient(90deg, rgba(214,179,122,.14), rgba(214,179,122,.04));
      border-bottom:1px solid var(--line);
    }
    .dj-cert2-card .topline b{
      color:var(--ink);
      font-size:13px;
      letter-spacing:.2px;
    }
    .dj-cert2-card .topline span{
      color:var(--muted);
      font-size:12px;
      font-weight:800;
    }

    .dj-grid{
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap:0;
    }
    .dj-item{
      display:grid;
      grid-template-columns: 140px 1fr;
      gap:10px;
      padding:11px 14px;
      border-bottom:1px dashed #eef2f7;
    }
    .dj-item:nth-child(odd){
      background:#fff;
    }
    .dj-item:nth-child(even){
      background:#fdfbf7;
    }
    .dj-k{
      color:var(--ink);
      font-weight:900;
      font-size:12px;
      text-transform:uppercase;
      letter-spacing:.35px;
    }
    .dj-v{
      color:#374151;
      font-weight:700;
      font-size:13px;
      text-align:left;
      word-break:break-word;
    }
    .dj-full{
      grid-column:1 / -1;
    }

    /* Footer */
    .dj-cert2-f{
      padding:14px 18px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
      flex-wrap:wrap;
      border-top:1px solid var(--line);
      background: #fff;
    }
    .dj-cert2-f .left{
      color:var(--muted);
      font-size:12px;
      font-weight:800;
    }
    .dj-cert2-f a{
      color:#111827;
      font-weight:900;
      text-decoration:none;
      border-bottom:2px solid rgba(214,179,122,.6);
    }

    /* Responsive */
    @media (max-width:900px){
      .dj-cert2-b{grid-template-columns:1fr; }
      .dj-cert2-qr{align-items:flex-start}
      .dj-cert2-qr img{width:150px;height:150px}
      .dj-item{grid-template-columns: 130px 1fr;}
      .dj-grid{grid-template-columns:1fr;}
    }
  </style>

  <div class="dj-cert2">
    <div class="dj-cert2-h">
      <div class="dj-cert2-brand">
        <div class="name"><?php echo esc_html($atts['store_name']); ?></div>
        <div class="sub">GEMSTONE CERTIFICATE • ONLINE VERIFICATION</div>
      </div>

      <div class="dj-cert2-badges">
        <div class="dj-chip">Report: <?php echo esc_html($report); ?></div>
        <div class="dj-chip">Date: <?php echo esc_html($date); ?></div>
      </div>
    </div>

    <div class="dj-cert2-b">
      <div class="dj-cert2-img">
        <?php if($img): ?><img src="<?php echo esc_url($img); ?>" alt="Gem Image"><?php endif; ?>
      </div>

      <div class="dj-cert2-card">
        <div class="topline">
          <b>Certificate Details</b>
          <span>Verified by QR</span>
        </div>

        <div class="dj-grid">
          <div class="dj-item"><div class="dj-k">Gem Type</div><div class="dj-v"><?php echo esc_html($val($type)); ?></div></div>
          <div class="dj-item"><div class="dj-k">Weight</div><div class="dj-v"><?php echo esc_html($val($weight)); ?><?php echo ($weight!=='' ? ' ct' : ''); ?></div></div>

          <div class="dj-item"><div class="dj-k">Shape</div><div class="dj-v"><?php echo esc_html($val($shape)); ?></div></div>
          <div class="dj-item"><div class="dj-k">Color</div><div class="dj-v"><?php echo esc_html($val($color)); ?></div></div>

          <div class="dj-item"><div class="dj-k">Transparency</div><div class="dj-v"><?php echo esc_html($val($transparency)); ?></div></div>
          <div class="dj-item"><div class="dj-k">Treatment</div><div class="dj-v"><?php echo esc_html($val($treatment)); ?></div></div>

          <div class="dj-item"><div class="dj-k">Origin</div><div class="dj-v"><?php echo esc_html($val($origin)); ?></div></div>
          <div class="dj-item"><div class="dj-k">Dimensions</div><div class="dj-v"><?php echo esc_html($val($dimensions)); ?></div></div>

          <div class="dj-item dj-full"><div class="dj-k">Comments</div><div class="dj-v"><?php echo esc_html($val($comments)); ?></div></div>
        </div>
      </div>

      <div class="dj-cert2-qr">
        <img src="<?php echo esc_url($qr); ?>" alt="QR Code">
        <small>
          Scan to verify this certificate online.<br>
          Verification is linked to this Report No.
        </small>
      </div>
    </div>

    <div class="dj-cert2-f">
      <div class="left">
        © <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($atts['store_name']); ?> — All rights reserved.
      </div>
      <div class="right">
        <a href="<?php echo esc_url($verify_url); ?>" target="_blank" rel="noopener">
          Open Verification Page
        </a>
      </div>
    </div>
  </div>
  <?php
  return ob_get_clean();
});
