<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Shop') — Bhutan Shop</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
:root{
  --pr:#c0392b;--pr2:#962d22;--pr-lt:#fff0ee;--pr-glow:rgba(192,57,43,.18);
  --sec:#f57c00;--sec-lt:#fff3e0;
  --ok:#2e7d32;--ok-lt:#e8f5e9;
  --err:#c62828;--err-lt:#ffebee;
  --warn:#e65100;--warn-lt:#fff3e0;
  --info:#1565c0;--info-lt:#e3f2fd;
  --bg:#f5f5f5;--card:#fff;--bdr:#e0e0e0;--bdr2:#eeeeee;
  --txt:#212121;--txt2:#424242;--mut:#9e9e9e;--mut2:#bdbdbd;
  --sh:0 1px 8px rgba(0,0,0,.08);--sh2:0 4px 20px rgba(0,0,0,.12);
  --r:4px;--r2:8px;--r3:12px;
  --font:'Plus Jakarta Sans',sans-serif;
  --hdr-h:56px;
}
body{font-family:var(--font);background:var(--bg);color:var(--txt);font-size:14px;line-height:1.5;}

/* ═══ HEADER ═══ */
.hdr{background:var(--pr);position:sticky;top:0;z-index:500;box-shadow:0 2px 8px rgba(0,0,0,.25);}
.hdr-top{max-width:1300px;margin:0 auto;padding:0 16px;display:flex;align-items:center;gap:12px;height:var(--hdr-h);}
.logo{display:flex;align-items:center;gap:8px;text-decoration:none;color:#fff;flex-shrink:0;}
.logo-mark{width:34px;height:34px;background:rgba(255,255,255,.15);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;}
.logo-words{display:flex;flex-direction:column;line-height:1.1;}
.logo-words b{font-size:15px;font-weight:800;letter-spacing:-.3px;}
.logo-words span{font-size:9px;opacity:.7;letter-spacing:.8px;text-transform:uppercase;}
/* Search */
.hdr-search{flex:1;max-width:560px;display:flex;height:38px;border-radius:4px;overflow:hidden;background:#fff;}
.hdr-search input{flex:1;padding:0 14px;border:none;font-family:var(--font);font-size:13.5px;color:var(--txt);outline:none;}
.hdr-search button{padding:0 18px;background:var(--sec);color:#fff;border:none;cursor:pointer;font-size:14px;transition:.2s;}
.hdr-search button:hover{background:#e65100;}
/* Nav */
.hdr-nav{display:flex;align-items:center;gap:2px;margin-left:auto;}
.hdr-link{display:flex;flex-direction:column;align-items:center;gap:1px;padding:6px 10px;color:rgba(255,255,255,.85);text-decoration:none;border-radius:4px;transition:.15s;cursor:pointer;border:none;background:none;font-family:var(--font);}
.hdr-link:hover{background:rgba(255,255,255,.12);color:#fff;}
.hdr-link i{font-size:16px;}
.hdr-link span{font-size:10px;font-weight:600;}
.cart-wrap{position:relative;}
.cart-badge{position:absolute;top:-2px;right:2px;background:var(--sec);color:#fff;border-radius:10px;min-width:18px;height:18px;padding:0 4px;font-size:10px;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1;}
/* User dropdown */
.usr-drop{position:relative;}
.usr-menu{display:none;position:absolute;right:0;top:calc(100% + 8px);background:#fff;border:1px solid var(--bdr);border-radius:var(--r2);min-width:200px;box-shadow:0 8px 32px rgba(0,0,0,.15);z-index:600;overflow:hidden;}
.usr-drop:hover .usr-menu{display:block;}
.usr-menu-hd{padding:14px 16px;background:var(--pr-lt);border-bottom:1px solid var(--bdr);}
.usr-menu-hd .name{font-weight:700;font-size:14px;color:var(--pr);}
.usr-menu-hd .role{font-size:11px;color:var(--mut);margin-top:1px;}
.usr-menu a,.usr-menu button{display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--txt2);text-decoration:none;font-size:13px;width:100%;border:none;background:none;font-family:var(--font);cursor:pointer;transition:.15s;}
.usr-menu a:hover,.usr-menu button:hover{background:#f5f5f5;}
.usr-menu .divider{height:1px;background:var(--bdr2);margin:4px 0;}

/* ═══ CATEGORY BAR ═══ */
.cat-bar{background:#fff;border-bottom:1px solid var(--bdr);position:sticky;top:var(--hdr-h);z-index:400;}
.cat-bar-inner{max-width:1300px;margin:0 auto;padding:0 16px;display:flex;gap:0;overflow-x:auto;scrollbar-width:none;}
.cat-bar-inner::-webkit-scrollbar{display:none;}
.cat-pill{display:flex;align-items:center;gap:6px;padding:9px 14px;font-size:12.5px;font-weight:600;color:var(--txt2);text-decoration:none;white-space:nowrap;border-bottom:2px solid transparent;transition:.15s;}
.cat-pill:hover{color:var(--pr);}
.cat-pill.active{color:var(--pr);border-bottom-color:var(--pr);}

/* ═══ MAIN LAYOUT ═══ */
.page-wrap{max-width:1300px;margin:0 auto;padding:16px;}
.shop-layout{display:grid;grid-template-columns:240px 1fr;gap:16px;align-items:start;}

/* ═══ SIDEBAR ═══ */
.sidebar-card{background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2);overflow:hidden;}
.sb-hd{padding:12px 16px;border-bottom:1px solid var(--bdr2);background:#fafafa;}
.sb-hd h3{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--mut);}
.sb-body{padding:12px 16px;}
/* Filter option */
.f-opt{display:flex;align-items:center;gap:8px;padding:5px 0;cursor:pointer;}
.f-opt input[type=checkbox],.f-opt input[type=radio]{accent-color:var(--pr);width:14px;height:14px;cursor:pointer;flex-shrink:0;}
.f-opt label{font-size:13px;cursor:pointer;color:var(--txt2);}
.f-opt:hover label{color:var(--pr);}
/* Price range */
.price-inputs{display:flex;gap:8px;align-items:center;margin-top:8px;}
.price-inputs input{width:80px;padding:5px 8px;border:1px solid var(--bdr);border-radius:var(--r);font-size:12px;color:var(--txt);}
.price-sep{color:var(--mut);font-size:12px;}

/* ═══ PRODUCT GRID ═══ */
.products-area{}
.products-toolbar{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--card);border:1px solid var(--bdr);border-radius:var(--r2) var(--r2) 0 0;border-bottom:none;font-size:13px;}
.products-toolbar .count{color:var(--mut);}
.sort-sel{padding:5px 10px;border:1px solid var(--bdr);border-radius:var(--r);font-size:12.5px;color:var(--txt);background:#fff;font-family:var(--font);cursor:pointer;outline:none;}
.sort-sel:focus{border-color:var(--pr);}
.prod-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:0;background:var(--card);border:1px solid var(--bdr);border-radius:0 0 var(--r2) var(--r2);overflow:hidden;}
.prod-grid .prod-card:nth-child(4n){border-right:none;}

/* ═══ PRODUCT CARD ═══ */
.prod-card{position:relative;background:var(--card);border-right:1px solid var(--bdr2);border-bottom:1px solid var(--bdr2);transition:box-shadow .2s;overflow:hidden;display:flex;flex-direction:column;}
.prod-card:hover{z-index:2;box-shadow:0 4px 24px rgba(0,0,0,.14);border-color:transparent;}
.prod-card:hover .card-actions{opacity:1;transform:translateY(0);}
.prod-card:hover .prod-img img{transform:scale(1.04);}
/* Image */
.prod-img-wrap{position:relative;aspect-ratio:1;overflow:hidden;background:#f5f5f5;}
.prod-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .35s ease;}
.prod-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;font-size:44px;}
/* Badges on image */
.card-badge-tl{position:absolute;top:8px;left:8px;display:flex;flex-direction:column;gap:4px;}
.badge-pill{display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;line-height:1.4;}
.badge-new{background:#e3f2fd;color:#1565c0;}
.badge-feat{background:#fff8e1;color:#e65100;}
.badge-oos{background:#ffebee;color:#c62828;}
.badge-sale{background:#fce4ec;color:#c62828;}
.badge-low{background:#fff3e0;color:#e65100;}
/* Wishlist */
.card-wishlist{position:absolute;top:8px;right:8px;width:30px;height:30px;background:#fff;border-radius:50%;border:1px solid var(--bdr);display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:0;transition:.2s;font-size:13px;color:var(--mut);text-decoration:none;}
.prod-card:hover .card-wishlist{opacity:1;}
.card-wishlist[style*="e91e63"]{opacity:1!important;}
/* Hover actions */
.card-actions{position:absolute;bottom:0;left:0;right:0;display:flex;gap:0;opacity:0;transform:translateY(6px);transition:all .22s ease;}
.btn-atc{flex:1;padding:9px 0;background:var(--sec);color:#fff;border:none;font-size:12.5px;font-weight:700;cursor:pointer;font-family:var(--font);transition:.15s;display:flex;align-items:center;justify-content:center;gap:6px;}
.btn-atc:hover{background:#e65100;}
.btn-atc.btn-view{background:var(--pr);flex:0 0 42px;}
.btn-atc.btn-view:hover{background:var(--pr2);}
.btn-atc:disabled,.btn-atc.oos{background:#bdbdbd;cursor:not-allowed;}
/* Card body */
.card-body{padding:10px 12px 12px;flex:1;display:flex;flex-direction:column;}
.card-brand{font-size:11px;color:var(--mut);font-weight:600;text-transform:uppercase;letter-spacing:.4px;margin-bottom:3px;}
.card-name{font-size:13px;font-weight:600;color:var(--txt);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;}
.card-name a{color:inherit;text-decoration:none;}
.card-name a:hover{color:var(--pr);}
/* Rating */
.card-rating{display:flex;align-items:center;gap:5px;margin-bottom:6px;}
.rating-badge{display:inline-flex;align-items:center;gap:3px;background:var(--ok);color:#fff;font-size:11px;font-weight:700;padding:2px 6px;border-radius:3px;}
.rating-count{font-size:11px;color:var(--mut);}
/* Price */
.card-price{display:flex;align-items:baseline;gap:8px;flex-wrap:wrap;margin-bottom:6px;}
.price-now{font-size:16px;font-weight:800;color:var(--txt);}
.price-was{font-size:12px;color:var(--mut);text-decoration:line-through;}
.price-off{font-size:12px;font-weight:700;color:var(--ok);}
/* Stock */
.card-stock{font-size:11.5px;font-weight:600;}
.stock-ok{color:var(--ok);}
.stock-low{color:var(--warn);}
.stock-none{color:var(--err);}
/* Variant chips preview */
.card-variants{display:flex;gap:4px;flex-wrap:wrap;margin-top:6px;}
.var-chip{width:18px;height:18px;border-radius:50%;border:1.5px solid var(--bdr);display:inline-block;cursor:pointer;}
.var-chip-text{padding:2px 6px;border-radius:3px;font-size:10px;font-weight:600;background:var(--bdr2);color:var(--txt2);}
.var-chip-more{font-size:11px;color:var(--mut);}

/* ═══ QUICK ADD PANEL (Side Drawer) ═══ */
#qap-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:900;opacity:0;pointer-events:none;transition:opacity .25s;}
#qap-overlay.open{opacity:1;pointer-events:all;}
#qap-panel{position:fixed;right:0;top:0;height:100%;width:420px;max-width:95vw;background:var(--card);z-index:901;transform:translateX(100%);transition:transform .3s cubic-bezier(.4,0,.2,1);overflow-y:auto;display:flex;flex-direction:column;}
#qap-panel.open{transform:translateX(0);}
.qap-hd{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--bdr);position:sticky;top:0;background:#fff;z-index:2;}
.qap-hd h2{font-size:15px;font-weight:700;}
.qap-close{width:32px;height:32px;border:1px solid var(--bdr);border-radius:50%;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:14px;color:var(--mut);transition:.15s;}
.qap-close:hover{background:var(--err-lt);color:var(--err);border-color:var(--err);}
.qap-body{padding:18px;flex:1;}
.qap-img-row{display:flex;gap:10px;margin-bottom:16px;}
.qap-main-img{width:100px;height:100px;border-radius:var(--r2);overflow:hidden;border:1px solid var(--bdr);background:#f5f5f5;flex-shrink:0;}
.qap-main-img img{width:100%;height:100%;object-fit:cover;transition:opacity .2s;}

.qap-thumbs{display:flex;flex-direction:column;gap:6px;}
.qap-thumb{width:44px;height:44px;border-radius:var(--r);overflow:hidden;border:2px solid transparent;cursor:pointer;background:#f5f5f5;}
.qap-thumb.active,.qap-thumb:hover{border-color:var(--pr);}
.qap-thumb img{width:100%;height:100%;object-fit:cover;}
.qap-info{flex:1;}
.qap-name{font-size:15px;font-weight:700;line-height:1.3;color:var(--txt);margin-bottom:4px;}
.qap-cat{font-size:11px;color:var(--mut);margin-bottom:8px;}
.qap-price-row{display:flex;align-items:baseline;gap:8px;margin-bottom:4px;}
.qap-price{font-size:22px;font-weight:800;color:var(--txt);}
.qap-price-was{font-size:13px;color:var(--mut);text-decoration:line-through;}
.qap-stock{font-size:12.5px;font-weight:600;margin-bottom:14px;padding:5px 0;}
.qap-stock.ok{color:var(--ok);}
.qap-stock.low{color:var(--warn);}
.qap-stock.none{color:var(--err);}
/* Attribute selector */
.qap-attr{margin-bottom:14px;}
.qap-attr-label{font-size:12px;font-weight:700;color:var(--txt2);margin-bottom:7px;display:flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:.4px;}
.qap-attr-label .sel-val{font-weight:400;color:var(--pr);text-transform:none;letter-spacing:0;}
.qap-opts{display:flex;gap:7px;flex-wrap:wrap;}
.qap-opt{padding:5px 12px;border:1.5px solid var(--bdr);border-radius:3px;font-size:13px;font-weight:500;cursor:pointer;transition:.15s;background:#fff;color:var(--txt2);}
.qap-opt:hover:not(.unavail){border-color:var(--pr);color:var(--pr);}
.qap-opt.selected{border-color:var(--pr);background:var(--pr);color:#fff;}
.qap-opt.unavail{opacity:.35;cursor:not-allowed;text-decoration:line-through;background:#f5f5f5;}
/* Qty + CTA */
.qap-qty-row{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.qty-ctrl{display:flex;align-items:center;border:1.5px solid var(--bdr);border-radius:var(--r);overflow:hidden;}
.qty-ctrl button{width:34px;height:36px;border:none;background:#f5f5f5;font-size:16px;font-weight:700;cursor:pointer;color:var(--txt2);transition:.15s;}
.qty-ctrl button:hover{background:var(--bdr);}
.qty-ctrl input{width:44px;height:36px;border:none;border-left:1.5px solid var(--bdr);border-right:1.5px solid var(--bdr);text-align:center;font-size:14px;font-weight:700;color:var(--txt);font-family:var(--font);}
.qap-cta{display:flex;gap:8px;}
.btn-add-cart{flex:1;padding:11px;background:var(--sec);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:8px;transition:.15s;}
.btn-add-cart:hover{background:#e65100;}
.btn-add-cart:disabled{background:var(--mut2);cursor:not-allowed;}
.btn-buy-now{flex:1;padding:11px;background:var(--pr);color:#fff;border:none;border-radius:var(--r);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:8px;transition:.15s;}
.btn-buy-now:hover{background:var(--pr2);}
.btn-buy-now:disabled{background:var(--mut2);cursor:not-allowed;}
.qap-desc{font-size:12.5px;color:var(--txt2);line-height:1.7;margin-top:14px;padding-top:14px;border-top:1px solid var(--bdr2);}
.qap-perks{display:flex;flex-direction:column;gap:8px;margin-top:14px;padding:12px;background:#f9f9f9;border-radius:var(--r2);}
.qap-perk{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--txt2);}
.qap-perk i{color:var(--ok);width:14px;}
.qap-go-detail{display:block;text-align:center;padding:10px;border:1px solid var(--bdr);border-radius:var(--r);color:var(--pr);font-size:13px;font-weight:600;text-decoration:none;margin-top:14px;transition:.15s;}
.qap-go-detail:hover{background:var(--pr-lt);}

/* ═══ SUCCESS TOAST ═══ */
#toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:#212121;color:#fff;padding:12px 20px;border-radius:var(--r2);font-size:13.5px;font-weight:600;display:flex;align-items:center;gap:10px;z-index:1000;opacity:0;pointer-events:none;transition:all .3s;white-space:nowrap;box-shadow:0 8px 24px rgba(0,0,0,.25);}
#toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
#toast i{color:#69f0ae;font-size:16px;}
#toast .toast-count{background:var(--sec);border-radius:20px;padding:2px 8px;font-size:12px;}

/* ═══ ALERTS ═══ */
.alert{padding:11px 14px;border-radius:var(--r2);margin-bottom:14px;display:flex;align-items:flex-start;gap:10px;font-size:13.5px;}
.alert-ok{background:var(--ok-lt);border:1px solid #a5d6a7;color:var(--ok);}
.alert-err{background:var(--err-lt);border:1px solid #ef9a9a;color:var(--err);}
.alert-warn{background:var(--warn-lt);border:1px solid #ffcc80;color:var(--warn);}
.alert-info{background:var(--info-lt);border:1px solid #90caf9;color:var(--info);}

/* ═══ FORMS ═══ */
.fg{margin-bottom:14px;}
.lbl{display:block;font-size:12px;font-weight:600;margin-bottom:5px;color:#555;letter-spacing:.2px;}
.req{color:var(--err);}
.fc{width:100%;padding:9px 12px;border:1.5px solid var(--bdr);border-radius:var(--r);font-size:14px;color:var(--txt);background:#fff;font-family:var(--font);transition:.2s;outline:none;}
.fc:focus{border-color:var(--pr);box-shadow:0 0 0 3px var(--pr-glow);}
textarea.fc{resize:vertical;}

/* ═══ BUTTONS ═══ */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--r);font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.15s;font-family:var(--font);}
.btn-pr{background:var(--pr);color:#fff;}.btn-pr:hover{background:var(--pr2);}
.btn-sec{background:var(--sec);color:#fff;}.btn-sec:hover{background:#e65100;}
.btn-sc{background:#fff;color:var(--txt);border:1.5px solid var(--bdr);}.btn-sc:hover{border-color:var(--pr);color:var(--pr);}
.btn-ok{background:var(--ok);color:#fff;}
.btn-err{background:var(--err);color:#fff;}
.btn-sm{padding:5px 10px;font-size:12px;}
.btn-xs{padding:3px 8px;font-size:11px;}
.btn-full{width:100%;justify-content:center;}

/* ═══ CARDS ═══ */
.card{background:var(--card);border-radius:var(--r2);border:1px solid var(--bdr);box-shadow:var(--sh);}
.card-hd{padding:13px 16px;border-bottom:1px solid var(--bdr2);display:flex;align-items:center;justify-content:space-between;}
.card-hd h2{font-size:13px;font-weight:700;}
.card-bd{padding:16px;}
.mb16{margin-bottom:16px;}
.breadcrumb{display:flex;gap:6px;align-items:center;margin-bottom:16px;font-size:12.5px;color:var(--mut);}
.breadcrumb a{color:var(--mut);text-decoration:none;}.breadcrumb a:hover{color:var(--pr);}
.sep{color:#ccc;}

/* ═══ BADGES ═══ */
.badge{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;}
.b-ok{background:var(--ok-lt);color:var(--ok);border:1px solid #c8e6c9;}
.b-err{background:var(--err-lt);color:var(--err);border:1px solid #ffcdd2;}
.b-warn{background:var(--warn-lt);color:var(--warn);border:1px solid #ffe0b2;}
.b-info{background:var(--info-lt);color:var(--info);border:1px solid #bbdefb;}
.b-sec{background:#f5f5f5;color:#616161;border:1px solid #e0e0e0;}
.b-pr{background:var(--pr-lt);color:var(--pr);border:1px solid #ffcdd2;}

/* ═══ FOOTER ═══ */
footer{background:#1a1a2e;color:rgba(255,255,255,.6);margin-top:40px;}
.ftr-top{max-width:1300px;margin:0 auto;padding:32px 16px;display:grid;grid-template-columns:1.5fr repeat(3,1fr);gap:32px;}
.ftr-brand .logo-big{font-size:28px;margin-bottom:10px;}
.ftr-brand h3{color:#fff;font-size:16px;font-weight:800;margin-bottom:6px;}
.ftr-brand p{font-size:12.5px;line-height:1.8;}
.ftr-col h4{color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-bottom:14px;}
.ftr-col a{display:block;color:rgba(255,255,255,.55);text-decoration:none;font-size:12.5px;line-height:2.2;}
.ftr-col a:hover{color:#fff;}
.ftr-btm{border-top:1px solid rgba(255,255,255,.08);padding:16px;text-align:center;font-size:12px;color:rgba(255,255,255,.3);}

/* ═══ CHIPS ═══ */
.chip{display:inline-flex;align-items:center;gap:4px;background:#f5f5f5;border:1px solid var(--bdr);border-radius:3px;padding:2px 8px;font-size:12px;}
.chip-pr{background:var(--pr-lt);border-color:#ffcdd2;color:var(--pr);}

/* ═══ EMPTY STATE ═══ */
.empty-state{text-align:center;padding:60px 20px;background:var(--card);border-radius:var(--r2);border:1px solid var(--bdr);}
.empty-state .icon{font-size:56px;margin-bottom:14px;}
.empty-state h2{font-size:18px;font-weight:700;margin-bottom:8px;}
.empty-state p{color:var(--mut);font-size:14px;}

/* ═══ RESPONSIVE ═══ */
@media(max-width:1100px){.prod-grid{grid-template-columns:repeat(3,1fr);}}
@media(max-width:900px){.shop-layout{grid-template-columns:1fr;}.sidebar-wrap{display:none;}.ftr-top{grid-template-columns:1fr 1fr;}}
@media(max-width:600px){.prod-grid{grid-template-columns:repeat(2,1fr);}.ftr-top{grid-template-columns:1fr;}}
</style>
@stack('styles')
</head>
<body>

<!-- HEADER -->
<header class="hdr">
<div class="hdr-top">
  <a href="{{ route('home') }}" class="logo">
    <div class="logo-mark">🐉</div>
    <div class="logo-words"><b>BhutanShop</b><span>Kingdom Store</span></div>
  </a>
  <form class="hdr-search" method="GET" action="{{ route('products.index') }}">
    <input type="text" name="search" placeholder="Search for products, brands and more" value="{{ request('search') }}" autocomplete="off">
    <button type="submit"><i class="fas fa-search"></i></button>
  </form>
  <nav class="hdr-nav">
    @auth
    <div class="usr-drop">
      <div class="hdr-link" style="min-width:64px;">
        <i class="fas fa-user-circle"></i>
        <span>{{ Str::limit(auth()->user()->name,8) }} ▾</span>
      </div>
      <div class="usr-menu">
        <div class="usr-menu-hd">
          <div class="name">{{ auth()->user()->name }}</div>
          <div class="role">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <a href="{{ route('orders.index') }}"><i class="fas fa-box-open"></i> My Orders</a>
        <a href="{{ route('wishlist.index') }}"><i class="fas fa-heart" style="color:#e91e63;"></i> My Wishlist</a>
        <a href="{{ route('profile.edit') }}"><i class="fas fa-user-cog"></i> Profile</a>
        @if(auth()->user()->isStaff())
        <div class="divider"></div>
        <a href="{{ route('admin.dashboard') }}" style="color:var(--pr);"><i class="fas fa-tachometer-alt"></i> Admin Panel</a>
        @endif
        <div class="divider"></div>
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button type="submit" style="color:var(--err);"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
      </div>
    </div>
    @else
    <a href="{{ route('login') }}" class="hdr-link"><i class="fas fa-sign-in-alt"></i><span>Login</span></a>
    <a href="{{ route('register') }}" class="hdr-link" style="background:rgba(255,255,255,.12);"><i class="fas fa-user-plus"></i><span>Register</span></a>
    @endauth
    @auth
    <a href="{{ route('wishlist.index') }}" class="hdr-link" style="position:relative;">
      <i class="far fa-heart"></i>
      <span>Wishlist</span>
      @php $wlCount = \App\Models\Wishlist::where('user_id',auth()->id())->count(); @endphp
      <span class="cart-badge" id="wlBadge" style="background:#e91e63;{{ $wlCount ? '' : 'display:none' }}">{{ $wlCount }}</span>
    </a>
    @endauth
    <a href="{{ route('cart.index') }}" class="hdr-link cart-wrap">
      <i class="fas fa-shopping-cart"></i>
      <span>Cart</span>
      @php $cartCount = app(\App\Services\CartService::class)->count(); @endphp
      <span class="cart-badge" id="cartBadge" style="{{ $cartCount ? '' : 'display:none' }}">{{ $cartCount }}</span>
    </a>
  </nav>
</div>
</header>

<!-- CATEGORY BAR -->
<nav class="cat-bar">
<div class="cat-bar-inner">
  <a href="{{ route('products.index') }}" class="cat-pill {{ !request('category') && !request('type') ? 'active' : '' }}">🏠 All</a>
  @foreach(\App\Models\Category::where('is_active',true)->orderBy('name')->get() as $cat)
  <a href="{{ route('products.index',['category'=>$cat->id]) }}" class="cat-pill {{ request('category')==$cat->id ? 'active' : '' }}">{{ $cat->name }}</a>
  @endforeach
</div>
</nav>

<!-- MAIN -->
<main>
@if(session('success'))<div class="alert alert-ok"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-err"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif
@if(session('info'))<div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>@endif
@if($errors->any())<div class="alert alert-err"><i class="fas fa-exclamation-triangle"></i><div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div></div>@endif
@yield('content')
</main>

<!-- FOOTER -->
<footer>
<div class="ftr-top">
  <div class="ftr-brand">
    <div class="logo-big">🐉</div>
    <h3>BhutanShop</h3>
    <p>Authentic products from the Kingdom of Bhutan. Gross National Happiness — one purchase at a time.</p>
    <p style="margin-top:12px;font-size:11px;color:rgba(255,255,255,.3);">All prices in BTN · Secure payments via Razorpay</p>
  </div>
  <div class="ftr-col">
    <h4>Shop</h4>
    @foreach(\App\Models\Category::where('is_active',true)->limit(6)->get() as $c)
    <a href="{{ route('products.index',['category'=>$c->id]) }}">{{ $c->name }}</a>
    @endforeach
  </div>
  <div class="ftr-col">
    <h4>Account</h4>
    @auth
    <a href="{{ route('orders.index') }}">My Orders</a>
    <a href="{{ route('cart.index') }}">My Cart</a>
    <a href="{{ route('profile.edit') }}">Profile Settings</a>
    @else
    <a href="{{ route('login') }}">Login</a>
    <a href="{{ route('register') }}">Create Account</a>
    @endauth
  </div>
  <div class="ftr-col">
    <h4>Policies</h4>
    <a href="#">Shipping Policy</a>
    <a href="#">Return Policy</a>
    <a href="#">Privacy Policy</a>
    <a href="#">Terms of Service</a>
  </div>
</div>
<div class="ftr-btm">© {{ date('Y') }} BhutanShop. All rights reserved. Made with ❤️ in the Kingdom of Bhutan.</div>
</footer>

<!-- QUICK ADD PANEL OVERLAY -->
<div id="qap-overlay" onclick="closeQAP()"></div>

<!-- QUICK ADD PANEL -->
<div id="qap-panel">
  <div class="qap-hd">
    <h2 id="qap-title">Add to Cart</h2>
    <button class="qap-close" onclick="closeQAP()"><i class="fas fa-times"></i></button>
  </div>
  <div class="qap-body" id="qap-body">
    <div style="text-align:center;padding:40px;color:var(--mut);"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i></div>
  </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toast"><i class="fas fa-check-circle"></i> <span id="toast-msg">Added to cart!</span> <span class="toast-count" id="toast-count"></span></div>

<script>
// ══════════════════════════════════════
//  QUICK ADD PANEL ENGINE
// ══════════════════════════════════════
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let qapProduct = null;
let qapSelectedOpts = {};

function openQAP(productId) {
  document.getElementById('qap-overlay').classList.add('open');
  document.getElementById('qap-panel').classList.add('open');
  document.body.style.overflow = 'hidden';
  document.getElementById('qap-body').innerHTML = '<div style="text-align:center;padding:40px;color:var(--mut);"><i class="fas fa-spinner fa-spin" style="font-size:28px;"></i></div>';
  fetch(`/cart/quick-view/${productId}`)
    .then(r => r.json())
    .then(data => { qapProduct = data; renderQAP(data); })
    .catch(() => { document.getElementById('qap-body').innerHTML = '<div class="alert alert-err">Failed to load product.</div>'; });
}

function closeQAP() {
  document.getElementById('qap-overlay').classList.remove('open');
  document.getElementById('qap-panel').classList.remove('open');
  document.body.style.overflow = '';
}

function renderQAP(p) {
  qapSelectedOpts = {};
  const imgs = [p.image_1, p.image_2, p.image_3].filter(Boolean);
  
  // Auto-select default variant options
  if (p.type === 'variant' && p.default_variant_option_ids?.length) {
    // We need to figure out which option belongs to which attribute
    p.attributes.forEach(attr => {
      const defaultOpt = attr.options.find(o => p.default_variant_option_ids.includes(o.id));
      if (defaultOpt) qapSelectedOpts[attr.id] = defaultOpt.id;
    });
  }

  let html = `
    <div class="qap-img-row">
      <div class="qap-main-img" id="qap-main-img">
        ${imgs.length ? `<img src="${imgs[0]}" id="qap-main-img-el" alt="">` : '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ddd;font-size:36px;"><i class="fas fa-image"></i></div>'}
      </div>
      ${imgs.length > 1 ? `<div class="qap-thumbs">${imgs.map((img,i)=>`<div class="qap-thumb ${i===0?'active':''}" onclick="qapSetImg('${img}',this)"><img src="${img}" alt=""></div>`).join('')}</div>` : ''}
      <div class="qap-info">
        ${p.category_name ? `<div class="qap-cat">${p.category_name}</div>` : ''}
        <div class="qap-name">${p.name}</div>
        <div class="qap-price-row">
          <div class="qap-price" id="qap-price">BTN ${formatPrice(p.price)}</div>
        </div>
        <div class="qap-stock" id="qap-stock"></div>
      </div>
    </div>`;

  // Attributes
  if (p.type === 'variant' && p.attributes.length) {
    html += '<div id="qap-attrs">';
    p.attributes.forEach(attr => {
      const selectedId = qapSelectedOpts[attr.id];
      const selectedLabel = selectedId ? attr.options.find(o=>o.id===selectedId)?.value : '';
      html += `
        <div class="qap-attr">
          <div class="qap-attr-label">${attr.name}: <span class="sel-val" id="qap-attr-sel-${attr.id}">${selectedLabel || 'Select...'}</span></div>
          <div class="qap-opts" id="qap-opts-${attr.id}">
            ${attr.options.map(opt => `
              <div class="qap-opt ${!opt.available?'unavail':''} ${selectedId===opt.id?'selected':''}" 
                   data-attr="${attr.id}" data-opt="${opt.id}" data-label="${opt.value}"
                   onclick="${opt.available?`qapSelectOpt(${attr.id},${opt.id},'${opt.value}',this)`:''}">
                ${opt.value}
              </div>`).join('')}
          </div>
        </div>`;
    });
    html += '</div>';
  }

  // Qty + Buttons
  html += `
    <div class="qap-qty-row">
      <div class="qty-ctrl">
        <button type="button" onclick="qapQty(-1)">−</button>
        <input type="number" id="qap-qty" value="1" min="1" max="99">
        <button type="button" onclick="qapQty(1)">+</button>
      </div>
      <span id="qap-variant-name" style="font-size:12px;color:var(--mut);"></span>
    </div>
    <div class="qap-cta">
      <button class="btn-add-cart" id="qap-atc-btn" onclick="qapAddToCart(false)">
        <i class="fas fa-shopping-cart"></i> Add to Cart
      </button>
      <button class="btn-buy-now" id="qap-buy-btn" onclick="qapAddToCart(true)">
        <i class="fas fa-bolt"></i> Buy Now
      </button>
    </div>`;

  if (p.description) {
    html += `<div class="qap-desc">${p.description.substring(0,200)}${p.description.length>200?'...':''}</div>`;
  }

  html += `
    <div class="qap-perks">
      <div class="qap-perk"><i class="fas fa-shield-alt"></i> Secure payment — SSL encrypted</div>
      <div class="qap-perk"><i class="fas fa-truck"></i> Free shipping on orders over BTN 5,000</div>
      <div class="qap-perk"><i class="fas fa-undo"></i> Easy returns within 7 days</div>
    </div>
    <a href="/products/${p.slug}" class="qap-go-detail"><i class="fas fa-expand"></i> View Full Details</a>`;

  document.getElementById('qap-body').innerHTML = html;
  document.getElementById('qap-title').textContent = p.name.length > 30 ? p.name.substring(0,30)+'…' : p.name;
  
  updateQAPState();
}

function qapSetImg(src, el) {
  const main = document.getElementById('qap-main-img-el');
  if (main) main.src = src;
  document.querySelectorAll('.qap-thumb').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}

function qapSelectOpt(attrId, optId, label, el) {
  qapSelectedOpts[attrId] = optId;
  document.querySelectorAll(`.qap-opt[data-attr="${attrId}"]`).forEach(o => o.classList.remove('selected'));
  el.classList.add('selected');
  const selLabel = document.getElementById(`qap-attr-sel-${attrId}`);
  if (selLabel) selLabel.textContent = label;
  updateQAPState();
}

function updateQAPState() {
  if (!qapProduct) return;
  const p = qapProduct;
  const atcBtn = document.getElementById('qap-atc-btn');
  const buyBtn = document.getElementById('qap-buy-btn');
  const stockEl = document.getElementById('qap-stock');
  const priceEl = document.getElementById('qap-price');
  const varName = document.getElementById('qap-variant-name');

  if (p.type === 'simple') {
    const inStock = p.stock_type === 'unlimited' || p.stock > 0;
    stockEl.className = 'qap-stock ' + (inStock ? (p.stock > 0 && p.stock < 5 ? 'low' : 'ok') : 'none');
    stockEl.innerHTML = inStock
      ? (p.stock_type === 'unlimited' ? '<i class="fas fa-check-circle"></i> In Stock'
        : p.stock < 5 ? `<i class="fas fa-exclamation-triangle"></i> Only ${p.stock} left!`
        : `<i class="fas fa-check-circle"></i> In Stock`)
      : '<i class="fas fa-times-circle"></i> Out of Stock';
    if (atcBtn) { atcBtn.disabled = !inStock; buyBtn.disabled = !inStock; }
    return;
  }

  // ── Cross-attribute availability: disable options incompatible with current selections ──
  // For each attribute, check which of its options form a valid in-stock combination
  // given the currently selected options of OTHER attributes.
  p.attributes.forEach(thisAttr => {
    const otherSelections = Object.entries(qapSelectedOpts)
      .filter(([aid]) => parseInt(aid) !== thisAttr.id)
      .map(([,oid]) => parseInt(oid));

    thisAttr.options.forEach(opt => {
      const el = document.querySelector(`.qap-opt[data-attr="${thisAttr.id}"][data-opt="${opt.id}"]`);
      if (!el) return;
      // Check if ANY variant contains this option AND all other selected options AND is in stock
      const compatible = Object.entries(p.variant_map).some(([key, v]) => {
        if (!v.in_stock) return false;
        const vOptIds = key.split(',').map(Number);
        const hasThis = vOptIds.includes(opt.id);
        const hasOthers = otherSelections.every(oid => vOptIds.includes(oid));
        return hasThis && hasOthers;
      });
      if (compatible) {
        el.classList.remove('unavail');
        el.style.opacity = '';
        el.style.textDecoration = '';
        el.style.cursor = 'pointer';
        el.onclick = () => qapSelectOpt(thisAttr.id, opt.id, opt.value, el);
      } else {
        el.classList.add('unavail');
        el.style.opacity = '0.35';
        el.style.textDecoration = 'line-through';
        el.style.cursor = 'not-allowed';
        el.onclick = null;
        // If this option was selected and is now incompatible, deselect it
        if (qapSelectedOpts[thisAttr.id] === opt.id) {
          delete qapSelectedOpts[thisAttr.id];
          const lbl = document.getElementById(`qap-attr-sel-${thisAttr.id}`);
          if (lbl) lbl.textContent = 'Select...';
          el.classList.remove('selected');
        }
      }
    });
  });

  // Variant product — check if all selected
  const allSelected = p.attributes.every(a => qapSelectedOpts[a.id]);
  if (!allSelected) {
    stockEl.className = 'qap-stock';
    stockEl.innerHTML = '<span style="color:var(--mut);font-size:13px;"><i class="fas fa-hand-point-down"></i> Select all options to continue</span>';
    if (atcBtn) { atcBtn.disabled = true; buyBtn.disabled = true; }
    if (varName) varName.textContent = '';
    return;
  }

  const key = Object.values(qapSelectedOpts).map(Number).sort((a,b)=>a-b).join(',');
  const v = p.variant_map[key];
  if (v) {
    priceEl.textContent = 'BTN ' + formatPrice(v.price);
    if (varName) { varName.textContent = v.name; varName.style.color = 'var(--ok)'; }
    const inStock = v.in_stock;
    stockEl.className = 'qap-stock ' + (inStock ? (v.stock > 0 && v.stock < 5 ? 'low' : 'ok') : 'none');
    stockEl.innerHTML = inStock
      ? (v.stock_type === 'unlimited' ? '<i class="fas fa-check-circle"></i> In Stock'
        : v.stock < 5 ? `<i class="fas fa-exclamation-triangle"></i> Only ${v.stock} left!`
        : `<i class="fas fa-check-circle"></i> In Stock`)
      : '<i class="fas fa-times-circle"></i> Out of Stock';
    if (atcBtn) { atcBtn.disabled = !inStock; buyBtn.disabled = !inStock; }
    if (atcBtn && inStock) atcBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
    // Update variant images
    const vImgs = [v.image_1, v.image_2, v.image_3].filter(Boolean);
    if (vImgs.length) {
      const main = document.getElementById('qap-main-img-el');
      if (main) { main.style.opacity='0'; setTimeout(()=>{main.src=vImgs[0];main.style.opacity='1';},120); }
    }
  } else {
    stockEl.className = 'qap-stock none';
    stockEl.innerHTML = '<i class="fas fa-times-circle"></i> Combination not available';
    if (atcBtn) { atcBtn.disabled = true; buyBtn.disabled = true; }
  }
}

function qapQty(d) {
  const inp = document.getElementById('qap-qty');
  if (!inp) return;
  inp.value = Math.max(1, parseInt(inp.value||1) + d);
}

async function qapAddToCart(buyNow) {
  if (!qapProduct) return;
  const p = qapProduct;
  const qty = parseInt(document.getElementById('qap-qty')?.value || 1);
  
  let variantId = null;
  if (p.type === 'variant') {
    const key = Object.values(qapSelectedOpts).map(Number).sort((a,b)=>a-b).join(',');
    const v = p.variant_map[key];
    if (!v || !v.in_stock) return;
    variantId = v.id;
  }

  const btn = document.getElementById('qap-atc-btn');
  const orig = btn?.innerHTML;
  if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

  try {
    const res = await fetch('/cart/add', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({product_id: p.id, variant_id: variantId, qty})
    });
    const data = await res.json();
    updateCartBadge(data.count);
    showToast('Added to cart!', data.count);
    if (buyNow) { closeQAP(); window.location.href = '/cart'; }
    else closeQAP();
  } catch(e) {
    showToast('Something went wrong', 0);
  } finally {
    if (btn && orig) btn.innerHTML = orig;
  }
}

// Direct add for simple products (no panel needed)
async function directAddToCart(productId, variantId, btn) {
  const orig = btn?.innerHTML;
  if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; btn.disabled = true; }
  try {
    const res = await fetch('/cart/add', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({product_id: productId, variant_id: variantId, qty: 1})
    });
    const data = await res.json();
    updateCartBadge(data.count);
    showToast('Added to cart!', data.count);
  } catch(e) { showToast('Error adding to cart'); }
  finally { if (btn && orig) { btn.innerHTML = orig; btn.disabled = false; } }
}

function updateCartBadge(count) {
  const badge = document.getElementById('cartBadge');
  if (!badge) return;
  badge.textContent = count;
  badge.style.display = count > 0 ? '' : 'none';
  badge.style.transform = 'scale(1.4)';
  setTimeout(() => badge.style.transform = '', 300);
}

let toastTimer;
function showToast(msg, count) {
  const t = document.getElementById('toast');
  const m = document.getElementById('toast-msg');
  const c = document.getElementById('toast-count');
  if (m) m.textContent = msg;
  if (c) { c.textContent = count > 0 ? `${count} in cart` : ''; c.style.display = count > 0 ? '' : 'none'; }
  t.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => t.classList.remove('show'), 2800);
}

function formatPrice(p) {
  return parseFloat(p).toLocaleString('en-IN', {minimumFractionDigits:2,maximumFractionDigits:2});
}

// Close panel on Escape key
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQAP(); });

// ══════════════════════════════════════
//  WISHLIST CARD FUNCTIONS
// ══════════════════════════════════════
async function cardToggleWishlist(productId, btn) {
  try {
    const res  = await fetch('/wishlist/toggle', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
      body: JSON.stringify({product_id: productId})
    });
    const data = await res.json();
    const icon = document.getElementById('wlcardicon_'+productId);
    const badge = document.getElementById('wlBadge');
    if (data.status === 'added') {
      if (icon) icon.className = 'fas fa-heart';
      btn.style.color = '#e91e63';
      btn.style.borderColor = '#e91e63';
      btn.style.background = '#fff0f4';
      btn.style.opacity = '1';
    } else {
      if (icon) icon.className = 'far fa-heart';
      btn.style.color = '';
      btn.style.borderColor = '';
      btn.style.background = '';
    }
    if (badge) { badge.textContent = data.count; badge.style.display = data.count > 0 ? '' : 'none'; }
    showToast(data.message, 0);
  } catch(e) { console.error('Wishlist error:', e); }
}

// Load wishlist state on page init for all card buttons
document.addEventListener('DOMContentLoaded', () => {
  @auth
  fetch('/wishlist/ids').then(r => r.json()).then(data => {
    if (!data.ids) return;
    data.ids.forEach(pid => {
      const icon = document.getElementById('wlcardicon_'+pid);
      const btn  = document.getElementById('wlcard_'+pid);
      if (icon) icon.className = 'fas fa-heart';
      if (btn)  { btn.style.color='#e91e63'; btn.style.borderColor='#e91e63'; btn.style.background='#fff0f4'; btn.style.opacity='1'; }
    });
  }).catch(()=>{});
  @endauth
});
</script>
@stack('scripts')
</body>
</html>
