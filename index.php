<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a237e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="আমার মামলা">
    <link rel="manifest" href="manifest.json">
    <title>আমার মামলা - দৈনিক কার্যতালিকা</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #1a237e;
            --primary-light: #534bae;
            --primary-dark: #000051;
            --accent: #ff6f00;
            --accent-light: #ffa040;
            --bg: #f5f5f5;
            --card: #ffffff;
            --text: #212121;
            --text-secondary: #757575;
            --border: #e0e0e0;
            --success: #2e7d32;
            --danger: #c62828;
            --shadow: 0 2px 8px rgba(0,0,0,0.12);
            --shadow-lg: 0 4px 16px rgba(0,0,0,0.16);
            --radius: 12px;
        }

        body {
            font-family: 'Noto Sans Bengali', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            padding-bottom: 80px;
            -webkit-font-smoothing: antialiased;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            padding: 16px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-lg);
        }
        .header h1 {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .header h1 svg { flex-shrink: 0; }
        .header-sub {
            font-size: 12px;
            opacity: 0.85;
            margin-top: 2px;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--card);
            display: flex;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.1);
            z-index: 100;
            border-top: 1px solid var(--border);
        }
        .nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 4px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: none;
            color: var(--text-secondary);
            font-family: inherit;
            font-size: 11px;
            gap: 2px;
        }
        .nav-item.active {
            color: var(--primary);
        }
        .nav-item svg {
            width: 24px;
            height: 24px;
        }
        .nav-item.active svg { fill: var(--primary); }

        /* Pages */
        .page {
            display: none;
            padding: 16px;
            animation: fadeIn 0.3s ease;
        }
        .page.active { display: block; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cards */
        .card {
            background: var(--card);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: var(--shadow);
        }
        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 14px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            background: var(--card);
            color: var(--text);
            transition: border-color 0.2s;
            appearance: none;
            -webkit-appearance: none;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }
        select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23757575' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 36px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            box-shadow: 0 4px 12px rgba(26,35,126,0.3);
        }
        .btn-primary:active {
            transform: scale(0.98);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: white;
            box-shadow: 0 4px 12px rgba(255,111,0,0.3);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        /* Causelist Table */
        .court-header {
            text-align: center;
            margin-bottom: 16px;
        }
        .court-header h2 {
            font-size: 18px;
            color: var(--primary);
            font-weight: 700;
        }
        .court-header .date-badge {
            display: inline-block;
            background: var(--accent);
            color: white;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 6px;
        }

        .case-list { list-style: none; }
        .case-item {
            background: var(--card);
            border-radius: var(--radius);
            padding: 14px;
            margin-bottom: 8px;
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary);
        }
        .case-item .case-sl {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 600;
        }
        .case-item .case-no {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin: 4px 0;
        }
        .case-item .case-activity {
            font-size: 13px;
            color: var(--primary);
            font-weight: 600;
            background: rgba(26,35,126,0.08);
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 4px;
        }
        .case-item .case-next {
            font-size: 12px;
            color: var(--success);
            margin-top: 4px;
        }
        .case-item .case-order {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Search */
        .search-box {
            position: relative;
        }
        .search-box input {
            padding-left: 42px;
        }
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        /* Saved Courts */
        .saved-court {
            display: flex;
            align-items: center;
            padding: 14px;
            background: var(--card);
            border-radius: var(--radius);
            margin-bottom: 8px;
            box-shadow: var(--shadow);
            cursor: pointer;
            transition: all 0.2s;
        }
        .saved-court:active { transform: scale(0.98); }
        .saved-court .court-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            flex-shrink: 0;
        }
        .saved-court .court-info {
            flex: 1;
            margin-left: 12px;
        }
        .saved-court .court-name {
            font-size: 14px;
            font-weight: 600;
        }
        .saved-court .court-detail {
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }
        .saved-court .delete-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: rgba(198,40,40,0.1);
            color: var(--danger);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }
        .empty-state svg {
            width: 80px;
            height: 80px;
            margin-bottom: 16px;
            opacity: 0.4;
        }
        .empty-state h3 {
            font-size: 16px;
            margin-bottom: 8px;
            color: var(--text);
        }
        .empty-state p {
            font-size: 13px;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 40px;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .loading-text {
            font-size: 14px;
            color: var(--text-secondary);
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 90px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--text);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 200;
            transition: transform 0.3s;
            white-space: nowrap;
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
        }

        /* Date picker row */
        .date-row {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        .date-row .form-group { flex: 1; margin-bottom: 0; }
        .date-row .btn { width: auto; padding: 12px 16px; }

        /* Quick date buttons */
        .quick-dates {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .quick-date-btn {
            padding: 6px 14px;
            border: 2px solid var(--border);
            border-radius: 20px;
            background: var(--card);
            font-size: 12px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            color: var(--text);
            transition: all 0.2s;
        }
        .quick-date-btn.active {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }

        /* Case count badge */
        .case-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--accent);
            color: white;
            min-width: 24px;
            height: 24px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            padding: 0 6px;
        }

        @media (min-width: 480px) {
            .page { padding: 20px; max-width: 600px; margin: 0 auto; }
        }
    </style>
</head>
<body>

<!-- Authentication Overlay -->
<div id="authOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: white; z-index: 2000; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; text-align: center;">
    <div style="margin-bottom: 24px;">
        <div style="font-size: 32px; font-weight: 800; color: var(--primary); margin-bottom: 8px;">আমার মামলা</div>
        <div style="color: var(--text-secondary); font-size: 14px;">আপনার মামলার ডিজিটাল ডায়েরি</div>
    </div>
    <div class="card" style="width: 100%; max-width: 320px;">
        <div class="card-title" id="authTitle">লগইন</div>
        
        <div id="loginFields">
            <div class="form-group">
                <label>মোবাইল নম্বর</label>
                <input type="text" class="form-control" id="authPhone" placeholder="০১৭XXXXXXXX">
            </div>
            <div class="form-group">
                <label>পিন কোড (৪ ডিজিট)</label>
                <input type="password" class="form-control" id="authPin" placeholder="XXXX" maxlength="4" inputmode="numeric">
            </div>
            <button class="btn btn-primary" onclick="handleAuth('login')">প্রবেশ করুন</button>
            <div style="margin-top: 15px; font-size: 13px;">
                অ্যাকাউন্ট নেই? <a href="#" onclick="showAuthMode('register')">রেজিস্ট্রেশন করুন</a>
            </div>
            <div style="margin-top: 8px; font-size: 13px;">
                <a href="#" onclick="showAuthMode('recovery')" style="color: var(--danger);">পিন ভুলে গেছেন?</a>
            </div>
        </div>

        <div id="registerFields" style="display: none;">
            <div class="form-group">
                <label>মোবাইল নম্বর</label>
                <input type="text" class="form-control" id="regPhone" placeholder="০১৭XXXXXXXX">
            </div>
            <div class="form-group">
                <label>নতুন পিন (৪ ডিজিট)</label>
                <input type="password" class="form-control" id="regPin" placeholder="XXXX" maxlength="4" inputmode="numeric">
            </div>
            <div class="form-group">
                <label>নিরাপত্তা প্রশ্ন: আপনার প্রিয় রং কি?</label>
                <input type="text" class="form-control" id="regSecurity" placeholder="যেমন: লাল/নীল (পিন রিসেট করতে লাগবে)">
            </div>
            <button class="btn btn-primary" onclick="handleAuth('register')">রেজিস্ট্রেশন সম্পন্ন করুন</button>
            <div style="margin-top: 15px; font-size: 13px;">
                ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="#" onclick="showAuthMode('login')">লগইন করুন</a>
            </div>
        </div>

        <div id="recoveryFields" style="display: none;">
            <div class="form-group">
                <label>রেজিস্টার্ড মোবাইল নম্বর</label>
                <input type="text" class="form-control" id="recPhone" placeholder="০১৭XXXXXXXX">
            </div>
            <div class="form-group">
                <label>নিরাপত্তা উত্তর (আপনার প্রিয় রং)</label>
                <input type="text" class="form-control" id="recSecurity" placeholder="রেজিস্ট্রেশনের সময় যা দিয়েছিলেন">
            </div>
            <div class="form-group">
                <label>নতুন পিন কোড সেট করুন</label>
                <input type="password" class="form-control" id="recNewPin" placeholder="XXXX" maxlength="4" inputmode="numeric">
            </div>
            <button class="btn btn-primary" onclick="handleAuth('recovery')">পিন রিসেট করুন</button>
            <div style="margin-top: 15px; font-size: 13px;">
                মনে পড়েছে? <a href="#" onclick="showAuthMode('login')">লগইন করুন</a>
            </div>
        </div>

        <p style="font-size: 11px; color: var(--text-secondary); margin-top: 12px;">
            তথ্য হারানো রোধ করতে আপনার ডাটাবেজে ডাটা সেভ রাখা হবে।
        </p>
    </div>
</div>

<!-- Header -->
<div class="header">
    <h1>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        আমার মামলা
    </h1>
    <div class="header-sub">বাংলাদেশ বিচার বিভাগ - দৈনিক কার্যতালিকা</div>
</div>

<!-- Page: Home / Causelist -->
<div class="page active" id="page-home">
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M12 4V2A10 10 0 0 0 2 12h2a8 8 0 0 1 8-8zm0 0V2a10 10 0 0 1 10 10h-2a8 8 0 0 0-8-8z"/></svg>
            আদালত নির্বাচন করুন
        </div>

        <div class="form-group">
            <label>বিভাগ</label>
            <select class="form-control" id="division" disabled>
                <option value="">লোড হচ্ছে...</option>
            </select>
        </div>

        <div class="form-group">
            <label>জেলা</label>
            <select class="form-control" id="district" disabled>
                <option value="">বিভাগ নির্বাচন করুন</option>
            </select>
        </div>

        <div class="form-group">
            <label>আদালতের ধরন</label>
            <select class="form-control" id="courtLayer" disabled>
                <option value="">জেলা নির্বাচন করুন</option>
            </select>
        </div>

        <div class="form-group">
            <label>আদালত</label>
            <select class="form-control" id="lowerCourt" disabled>
                <option value="">আদালতের ধরন নির্বাচন করুন</option>
            </select>
        </div>

        <div class="quick-dates" id="quickDates"></div>

        <div class="date-row">
            <div class="form-group">
                <label>তারিখ</label>
                <input type="date" class="form-control" id="caseDate">
            </div>
            <button class="btn btn-primary" id="btnSearch" disabled>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                খুঁজুন
            </button>
        </div>
    </div>

    <div id="causelistResult"></div>
</div>

<!-- Install App Card -->
<div class="card" id="installCard" style="display:block; background: linear-gradient(135deg, var(--primary), var(--accent)); color: white; margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="background: white; border-radius: 10px; padding: 5px; display: flex; align-items: center; justify-content: center;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="var(--primary)"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 16px;">অ্যাপটি ডাউনলোড করুন</div>
                <div style="font-size: 11px; opacity: 0.9;">সহজ ব্যবহারের জন্য ফোনে ইন্সটল করুন</div>
            </div>
        </div>
        <button class="btn btn-sm" id="btnInstall" style="background: white; color: var(--primary); font-weight: 700; border: none; padding: 8px 15px;">ইন্সটল</button>
    </div>
</div>

<!-- Page: Case Search -->
<div class="page" id="page-search">
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            মামলা অনুসন্ধান
        </div>

        <div class="form-group">
            <label>বিভাগ</label>
            <select class="form-control" id="searchDivision" disabled>
                <option value="">লোড হচ্ছে...</option>
            </select>
        </div>

        <div class="form-group">
            <label>জেলা</label>
            <select class="form-control" id="searchDistrict" disabled>
                <option value="">বিভাগ নির্বাচন করুন</option>
            </select>
        </div>

        <div class="form-group">
            <label>আদালতের ধরন</label>
            <select class="form-control" id="searchCourtLayer" disabled>
                <option value="">জেলা নির্বাচন করুন</option>
            </select>
        </div>

        <div class="form-group">
            <label>আদালত</label>
            <select class="form-control" id="searchLowerCourt" disabled>
                <option value="">আদালতের ধরন নির্বাচন করুন</option>
            </select>
        </div>

        <div class="form-group">
            <label>মামলার নম্বর</label>
            <input type="text" class="form-control" id="searchCaseNo" placeholder="যেমন: ৪৫৫/২০২৩">
        </div>

        <button class="btn btn-accent" id="btnCaseSearch" disabled>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            মামলা খুঁজুন
        </button>
    </div>

    <div id="caseSearchResult"></div>
</div>

<!-- Page: My Cases -->
<div class="page" id="page-mycases">
    <div class="card">
        <div id="upcomingCasesSection" style="margin-bottom: 20px; display: none;">
            <div style="font-weight: 700; font-size: 16px; margin-bottom: 10px; color: #d32f2f; display: flex; align-items: center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#d32f2f" style="margin-right:8px"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                আগামীকালের মামলাসমূহ
            </div>
            <div id="upcomingCasesList"></div>
            <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">
        </div>

        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            আমার সকল মামলা
        </div>
        <div style="background: rgba(26,35,126,0.05); padding: 12px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid var(--primary);">
            <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px; color: var(--primary);">ম্যানুয়ালি মামলা যোগ করুন</div>
            <div style="font-size: 11px; color: var(--text-secondary); margin-bottom: 8px;">সার্চ না করে সরাসরি ডাটা এন্ট্রি করতে চান? নিচের ফর্মটি ব্যবহার করুন।</div>
            
            <div class="form-group" style="margin-bottom: 8px;">
                <input type="text" class="form-control" id="manualCaseNo" placeholder="মামলা নম্বর (যেমন: ৪৫৫/২০২৩)" style="padding: 8px 12px; font-size: 14px;">
            </div>
            
            <div id="manualCourtSection" style="margin-bottom: 8px;">
                <div style="font-size: 11px; font-weight: 600; color: var(--primary); margin-bottom: 4px;">ভবিষ্যতে অটো-আপডেটের জন্য আদালত সিলেক্ট করুন:</div>
                <select class="form-control" id="manualDivision" style="padding: 6px; font-size: 13px; margin-bottom: 4px;">
                    <option value="">বিভাগ</option>
                </select>
                <select class="form-control" id="manualDistrict" disabled style="padding: 6px; font-size: 13px; margin-bottom: 4px;">
                    <option value="">জেলা</option>
                </select>
                <select class="form-control" id="manualCourtLayer" disabled style="padding: 6px; font-size: 13px; margin-bottom: 4px;">
                    <option value="">আদালতের ধরন</option>
                </select>
                <select class="form-control" id="manualLowerCourt" disabled style="padding: 6px; font-size: 13px; margin-bottom: 4px;">
                    <option value="">আদালত</option>
                </select>
                <div style="font-size: 10px; color: var(--text-secondary); text-align: center;">--- অথবা ---</div>
            </div>

            <div class="form-group" style="margin-bottom: 8px;">
                <input type="text" class="form-control" id="manualCourtName" placeholder="আদালতের নাম লিখুন (ম্যানুয়াল)" style="padding: 8px 12px; font-size: 14px;">
            </div>
            <div class="form-group" style="margin-bottom: 8px;">
                <input type="text" class="form-control" id="manualNextDate" placeholder="পরবর্তী তারিখ (যদি থাকে)" style="padding: 8px 12px; font-size: 14px;">
            </div>
            
            <!-- New: Party Info -->
            <div style="background: rgba(0,0,0,0.02); padding: 8px; border-radius: 6px; margin-bottom: 12px;">
                <div style="font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">বাদী ও বিবাদীর তথ্য:</div>
                <input type="text" class="form-control" id="manualPlaintiff" placeholder="বাদীর নাম ও ঠিকানা" style="padding: 6px; font-size: 13px; margin-bottom: 4px;">
                <input type="text" class="form-control" id="manualDefendant" placeholder="বিবাদীর নাম ও ঠিকানা" style="padding: 6px; font-size: 13px;">
            </div>

            <!-- New: PDF Attachment -->
            <div style="margin-bottom: 12px;">
                <div style="font-size: 11px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px;">মামলার পিডিএফ (PDF) যুক্ত করুন:</div>
                <input type="file" id="manualPdf" accept="application/pdf" style="font-size: 12px; width: 100%;">
            </div>

            <button class="btn btn-sm btn-primary" onclick="addCaseManually()" style="padding: 8px; font-size: 13px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="white" style="margin-right:4px"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>তালিকায় যোগ করুন
            </button>
        </div>
        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:12px;">
            আপনার ব্যক্তিগত মামলাগুলো এখানে যোগ করুন। অ্যাপটি প্রতিদিন অটোমেটিক চেক করবে এবং পরবর্তী তারিখ আসলে আপনাকে জানাবে।
        </p>
        <div id="myCasesList"></div>
    </div>
</div>

<!-- Page: Profile -->
<div class="page" id="page-profile">
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            ব্যবহারকারীর প্রোফাইল
        </div>
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 80px; height: 80px; background: #eee; border-radius: 50%; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="#999"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <h3 id="profileNameDisplay">ব্যবহারকারী</h3>
            <p id="profilePhoneDisplay" style="color: var(--text-secondary); font-size: 14px;">+৮৮০XXXXXXXXXX</p>
        </div>

        <div class="form-group">
            <label>আপনার নাম</label>
            <input type="text" class="form-control" id="inputProfileName" placeholder="আপনার নাম লিখুন">
        </div>
        <div class="form-group">
            <label>মোবাইল নম্বর</label>
            <input type="text" class="form-control" id="inputProfilePhone" placeholder="আপনার মোবাইল নম্বর">
        </div>
        <button class="btn btn-primary" onclick="saveProfile()">প্রোফাইল আপডেট করুন</button>

        <hr style="margin: 24px 0; border: 0; border-top: 1px solid #eee;">
        
        <div class="card-title" style="font-size: 16px;">ডাটা ব্যাকআপ ও রিস্টোর</div>
        <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 12px;">
            আপনার সেভ করা সকল মামলা এবং প্রোফাইল তথ্য বর্তমানে শুধুমাত্র এই ব্রাউজারে সংরক্ষিত আছে। অন্য ফোনে ব্যবহার করতে বা ব্যাকআপ রাখতে নিচের অপশনগুলো ব্যবহার করুন।
        </p>
        <div style="display: flex; gap: 8px;">
            <button class="btn btn-outline" style="flex: 1; font-size: 12px;" onclick="exportData()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--primary)" style="margin-right:4px"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>ব্যাকআপ নিন
            </button>
            <button class="btn btn-outline" style="flex: 1; font-size: 12px;" onclick="$('importFile').click()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="var(--primary)" style="margin-right:4px"><path d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/></svg>রিস্টোর করুন
            </button>
            <input type="file" id="importFile" style="display: none;" accept=".json" onchange="importData(event)">
        </div>
    </div>
</div>

<!-- Page: Saved Courts -->
<div class="page" id="page-saved">
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
            সংরক্ষিত আদালত
        </div>
        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:12px;">
            প্রায়শই ব্যবহৃত আদালত সংরক্ষণ করুন। প্রতিদিনের কার্যতালিকা দ্রুত দেখুন।
        </p>
        <div id="savedCourtsList"></div>
    </div>
</div>

<!-- Page: About -->
<div class="page" id="page-about">
    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="var(--primary)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            সম্পর্কে
        </div>
        <p style="font-size:14px;line-height:1.8;">
            এই অ্যাপটি বাংলাদেশ বিচার বিভাগের <strong>causelist.judiciary.gov.bd</strong> ওয়েবসাইট থেকে দৈনিক কার্যতালিকা প্রদর্শন করে।
        </p>
        <div style="margin-top:16px;padding:12px;background:rgba(26,35,126,0.06);border-radius:8px;font-size:13px;">
            <strong>বৈশিষ্ট্যসমূহ:</strong><br>
            ✅ দৈনিক কার্যতালিকা দেখুন<br>
            ✅ মামলা অনুসন্ধান করুন<br>
            ✅ প্রিয় আদালত সংরক্ষণ করুন<br>
            ✅ মোবাইল বান্ধব ডিজাইন<br>
            ✅ অফলাইনে সংরক্ষিত আদালত দেখুন
        </div>
        <div style="margin-top:16px;font-size:12px;color:var(--text-secondary);">
            তথ্যসূত্র: <a href="https://causelist.judiciary.gov.bd/" target="_blank" style="color:var(--primary)">causelist.judiciary.gov.bd</a><br>
            হেল্পলাইন: ৩৩৩ (টোল ফ্রি)
        </div>
    </div>
</div>

<!-- Bottom Navigation -->
<div class="bottom-nav">
    <button class="nav-item active" data-page="page-home">
        <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        কার্যতালিকা
    </button>
    <button class="nav-item" data-page="page-search">
        <svg viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        মামলা খুঁজুন
    </button>
    <button class="nav-item" data-page="page-mycases">
        <svg viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        আমার মামলা
    </button>
    <button class="nav-item" data-page="page-profile">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
        প্রোফাইল
    </button>
    <button class="nav-item" data-page="page-about">
        <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
        সম্পর্কে
    </button>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
const API = 'api.php';
let savedCourts = JSON.parse(localStorage.getItem('savedCourts') || '[]');
let myCases = JSON.parse(localStorage.getItem('myCases') || '[]');
let userProfile = JSON.parse(localStorage.getItem('userProfile') || '{"name": "ব্যবহারকারী", "phone": "", "user_id": 0}');

// --- Utility ---
function $(id) { return document.getElementById(id); }
function showToast(msg) {
    const t = $('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}
function loadingHtml() {
    return '<div class="loading"><div class="spinner"></div><div class="loading-text">লোড হচ্ছে...</div></div>';
}
function en2bn(num) {
    const bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    return String(num).replace(/[0-9]/g, d => bnDigits[d]);
}
function bn2en(num) {
    return String(num).replace(/[০-৯]/g, d => '০১২৩৪৫৬৭৮৯'[d.charCodeAt(0) - 0x9E6]);
}
function formatDateBn(dateStr) {
    if (!dateStr) return '';
    const parts = dateStr.split('-');
    if (parts.length === 3) {
        return en2bn(parts[0]) + '-' + en2bn(parts[1]) + '-' + en2bn(parts[2]);
    }
    return dateStr;
}

// --- Navigation ---
document.querySelectorAll('.nav-item').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const pageId = btn.dataset.page;
        $(pageId).classList.add('active');
        if (pageId === 'page-saved') renderSavedCourts();
    });
});

// --- Quick Date Buttons ---
function setupQuickDates() {
    const container = $('quickDates');
    container.innerHTML = '';
    const today = new Date();
    const dayNames = ['রবি','সোম','মঙ্গল','বুধ','বৃহ','শুক্র','শনি'];

    for (let i = -2; i <= 5; i++) {
        const d = new Date(today);
        d.setDate(d.getDate() + i);
        const day = d.getDay();
        // Skip Friday(5) and Saturday(6)
        if (day === 5 || day === 6) continue;

        const dateStr = d.toISOString().split('T')[0];
        const btn = document.createElement('button');
        btn.className = 'quick-date-btn' + (i === 0 ? ' active' : '');
        btn.textContent = dayNames[day] + ' ' + en2bn(d.getDate()) + '/' + en2bn(d.getMonth()+1);
        btn.dataset.date = dateStr;
        btn.addEventListener('click', () => {
            container.querySelectorAll('.quick-date-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            $('caseDate').value = dateStr;
        });
        container.appendChild(btn);
    }
    $('caseDate').value = today.toISOString().split('T')[0];
}

// --- Load Divisions ---
async function loadDivisions(selectId) {
    const sel = $(selectId);
    sel.disabled = true;
    sel.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    try {
        const res = await fetch(API + '?action=divisions');
        const data = await res.json();
        sel.innerHTML = '<option value="">বিভাগ নির্বাচন করুন</option>';
        for (const key in data) {
            const opt = document.createElement('option');
            opt.value = data[key].geo_division_id;
            opt.textContent = data[key].division_name_bng;
            sel.appendChild(opt);
        }
        sel.disabled = false;
    } catch (e) {
        sel.innerHTML = '<option value="">লোড ব্যর্থ</option>';
        console.error(e);
    }
}

// --- Load Districts ---
async function loadDistricts(divisionId, selectId) {
    const sel = $(selectId);
    sel.disabled = true;
    sel.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    try {
        const res = await fetch(API + '?action=districts&division_id=' + divisionId);
        const data = await res.json();
        sel.innerHTML = '<option value="">জেলা নির্বাচন করুন</option>';
        for (const key in data) {
            for (const d of data[key].districts) {
                const opt = document.createElement('option');
                opt.value = d.geo_district_id;
                opt.textContent = d.district_name_bng;
                sel.appendChild(opt);

                // Metropolitan option
                const metroCities = ['বরিশাল','চট্টগ্রাম','ঢাকা','রাজশাহী','খুলনা','সিলেট','গাজীপুর','রংপুর'];
                if (metroCities.includes(d.district_name_bng)) {
                    const optM = document.createElement('option');
                    optM.value = d.geo_district_id + '-1';
                    optM.textContent = d.district_name_bng + ' মহানগর';
                    sel.appendChild(optM);
                }
            }
        }
        sel.disabled = false;
    } catch (e) {
        sel.innerHTML = '<option value="">লোড ব্যর্থ</option>';
        console.error(e);
    }
}

// --- Court Layer Change ---
function setupCourtLayer(districtVal, layerSelectId) {
    const sel = $(layerSelectId);
    const va = districtVal.split('-');
    const isMetro = va.length > 1;

    if (isMetro) {
        sel.innerHTML = '<option value="0">আদালতের ধরন</option>' +
            '<option value="10,11,12,88">মহানগর দায়রা জজ আদালত</option>' +
            '<option value="13,22,23,24,78,92,100">চীফ মেট্রোপলিটন ম্যাজিস্ট্রেট আদালত</option>' +
            '<option value="109">বিশেষ ট্রাইব্যুনাল</option>';
    } else {
        sel.innerHTML = '<option value="0">আদালতের ধরন</option>' +
            '<option value="4,5,6,7,8,18,19,20,21,74,75,76,83,94,103,105">জেলা জজ আদালত</option>' +
            '<option value="14,15,16,17,84,85,91,95">চীফ জুডিসিয়াল ম্যাজিট্রেট আদালত</option>' +
            '<option value="89,9,72,73,77,79,80,81,82,86,87,96,102,104,110,111,112">ট্রাইব্যুনালসমূহ</option>';
    }
    sel.disabled = false;
}

// --- Load Lower Courts ---
async function loadLowerCourts(districtId, officeOriginId, selectId) {
    const sel = $(selectId);
    sel.disabled = true;
    sel.innerHTML = '<option value="">লোড হচ্ছে...</option>';
    try {
        const res = await fetch(API + '?action=courts&district_id=' + districtId + '&office_origin_id=' + officeOriginId);
        const data = await res.json();
        sel.innerHTML = '<option value="">আদালত নির্বাচন করুন</option>';
        for (const v in data) {
            if (data[v].id != 3198 && data[v].id != 3199) {
                const opt = document.createElement('option');
                opt.value = data[v].id;
                opt.textContent = data[v].office_name_bng;
                sel.appendChild(opt);
            }
        }
        sel.disabled = false;
    } catch (e) {
        sel.innerHTML = '<option value="">লোড ব্যর্থ</option>';
        console.error(e);
    }
}

// --- Fetch Causelist ---
async function fetchCauselist(courtId, date, containerId) {
    const container = $(containerId);
    container.innerHTML = loadingHtml();

    let url = API + '?action=causelist&courtId=' + courtId;
    if (date) url += '&date=' + encodeURIComponent(date);

    try {
        const res = await fetch(url);
        const data = await res.json();

        if (data.error) {
            container.innerHTML = '<div class="empty-state"><h3>তথ্য পাওয়া যায়নি</h3><p>' + data.error + '</p></div>';
            return;
        }

        if (!data.cases || data.cases.length === 0) {
            container.innerHTML = `
                <div class="court-header">
                    <h2>${data.court_name || ''}</h2>
                    ${data.date ? '<div class="date-badge">' + data.date + '</div>' : ''}
                </div>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="var(--text-secondary)"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    <h3>কোনো মামলা নেই</h3>
                    <p>এই তারিখে এই আদালতে কোনো মামলার তালিকা পাওয়া যায়নি</p>
                </div>`;
            return;
        }

        let html = `
            <div class="court-header">
                <h2>${data.court_name || ''}</h2>
                ${data.date ? '<div class="date-badge">' + data.date + '</div>' : ''}
                <div style="margin-top:8px"><span class="case-count">${en2bn(data.cases.length)}</span> <span style="font-size:13px;color:var(--text-secondary)">টি মামলা</span></div>
            </div>
            <ul class="case-list">`;

        for (const c of data.cases) {
            html += `
                <li class="case-item">
                    <div class="case-sl">ক্রমিক নং: ${c.sl}</div>
                    <div class="case-no">${c.case_no}</div>
                    <div class="case-activity">${c.activity}</div>
                    ${c.next_date ? '<div class="case-next">পরবর্তী তারিখ: ' + c.next_date + '</div>' : ''}
                    ${c.order ? '<div class="case-order">সংক্ষিপ্ত আদেশ: ' + c.order + '</div>' : ''}
                </li>`;
        }
        html += '</ul>';

        // Case info button
        const courtName = data.court_name || '';
        const caseNo = data.cases[0].case_no; // Just a placeholder check
        
        html += `<button class="btn btn-outline" style="margin-top:12px" onclick="saveCourt(${courtId}, '${courtName.replace(/'/g, "\\'")}')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--primary)"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
            আদালত সংরক্ষণ করুন
        </button>`;

        // Add to My Cases button if it's a single case view or similar
        container.innerHTML = html;
        
        // Add "Add to My Cases" buttons to each case item
        document.querySelectorAll('#' + containerId + ' .case-item').forEach((item, index) => {
            const caseData = data.cases[index];
            const btn = document.createElement('button');
            btn.className = 'btn btn-sm btn-accent';
            btn.style.marginTop = '8px';
            btn.style.fontSize = '12px';
            btn.style.padding = '6px 12px';
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="white" style="margin-right:4px"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>আমার মামলায় যোগ করুন';
            btn.onclick = () => addToMyCases(courtId, courtName, caseData);
            item.appendChild(btn);
        });
    } catch (e) {
        container.innerHTML = '<div class="empty-state"><h3>সংযোগ ব্যর্থ</h3><p>ইন্টারনেট সংযোগ পরীক্ষা করুন</p></div>';
        console.error(e);
    }
}

// --- Fetch Case Info ---
async function fetchCaseInfo(courtId, caseno, containerId) {
    const container = $(containerId);
    container.innerHTML = loadingHtml();

    // Normalize case number to Bengali digits for API
    const normalizedCaseNo = en2bn(caseno);

    try {
        const res = await fetch(API + '?action=caseinfo&courtId=' + courtId + '&caseno=' + encodeURIComponent(normalizedCaseNo));
        const data = await res.json();

        if (data.error) {
            container.innerHTML = '<div class="empty-state"><h3>তথ্য পাওয়া যায়নি</h3><p>' + data.error + '</p></div>';
            return;
        }

        if (!data.cases || data.cases.length === 0) {
            container.innerHTML = '<div class="empty-state"><h3>মামলা পাওয়া যায়নি</h3><p>মামলার নম্বর সঠিকভাবে লিখুন</p></div>';
            return;
        }

        let html = `
            <div class="court-header">
                <h2>${data.court_name || ''}</h2>
                ${data.date ? '<div class="date-badge">' + data.date + '</div>' : ''}
                <div style="margin-top:8px"><span class="case-count">${en2bn(data.cases.length)}</span> <span style="font-size:13px;color:var(--text-secondary)">টি মামলা পাওয়া গেছে</span></div>
            </div>
            <ul class="case-list">`;

        for (const c of data.cases) {
            html += `
                <li class="case-item">
                    <div class="case-sl">ক্রমিক নং: ${c.sl}</div>
                    <div class="case-no">${c.case_no}</div>
                    <div class="case-activity">${c.activity}</div>
                    ${c.next_date ? '<div class="case-next">পরবর্তী তারিখ: ' + c.next_date + '</div>' : ''}
                    ${c.order ? '<div class="case-order">সংক্ষিপ্ত আদেশ: ' + c.order + '</div>' : ''}
                </li>`;
        }
        html += '</ul>';
        container.innerHTML = html;
    } catch (e) {
        container.innerHTML = '<div class="empty-state"><h3>সংযোগ ব্যর্থ</h3><p>ইন্টারনেট সংযোগ পরীক্ষা করুন</p></div>';
        console.error(e);
    }
}

// --- Save/Load Courts ---
function saveCourt(id, name) {
    if (savedCourts.find(c => c.id === id)) {
        showToast('আদালতটি ইতিমধ্যে সংরক্ষিত আছে');
        return;
    }
    savedCourts.push({ id, name });
    localStorage.setItem('savedCourts', JSON.stringify(savedCourts));
    showToast('আদালত সংরক্ষিত হয়েছে!');
}

function deleteCourt(id) {
    savedCourts = savedCourts.filter(c => c.id !== id);
    localStorage.setItem('savedCourts', JSON.stringify(savedCourts));
    renderSavedCourts();
    showToast('আদালট মুছে ফেলা হয়েছে');
}

function renderSavedCourts() {
    const container = $('savedCourtsList');
    if (savedCourts.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="var(--text-secondary)"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                <h3>কোনো আদালত সংরক্ষিত নেই</h3>
                <p>কার্যতালিকা দেখার পর "আদালত সংরক্ষণ করুন" বোতামে ক্লিক করুন</p>
            </div>`;
        return;
    }

    let html = '';
    for (const court of savedCourts) {
        html += `
            <div class="saved-court" onclick="viewSavedCourt(${court.id})">
                <div class="court-icon">⚖</div>
                <div class="p-4 border-b">
                    <h3 class="font-bold text-lg mb-2">অ্যান্ড্রয়েড অ্যাপ ডাউনলোড করুন</h3>
                    <p class="text-sm text-gray-600 mb-4">আমাদের প্রফেশনাল অ্যান্ড্রয়েড অ্যাপটি ডাউনলোড করে আরও দ্রুত এবং সহজে মামলা ম্যানেজ করুন।</p>
                    <a href="signed/amarmamla-signed.apk" class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.523 15.3414L12 20.8644L6.477 15.3414H9V3.13559H15V15.3414H17.523ZM12 2L2 12H5V22H19V12H22L12 2Z"/></svg>
                        অ্যাপ ডাউনলোড করুন
                    </a>
                </div>
                
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2">এপ ইনস্টল করুন</h3>
                </div>
                <div class="court-info">
                    <div class="court-name">${court.name}</div>
                    <div class="court-detail">আদালত নং: ${en2bn(court.id)}</div>
                </div>
                <button class="delete-btn" onclick="event.stopPropagation();deleteCourt(${court.id})">✕</button>
            </div>`;
    }
    container.innerHTML = html;
}

function viewSavedCourt(courtId) {
    // Switch to home page and load causelist
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelector('[data-page="page-home"]').classList.add('active');
    $('page-home').classList.add('active');

    const date = $('caseDate').value;
    fetchCauselist(courtId, date, 'causelistResult');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// --- PWA Install Logic ---
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    $('installCard').style.display = 'block';
});

$('btnInstall').addEventListener('click', async () => {
    if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        if (outcome === 'accepted') {
            $('installCard').style.display = 'none';
        }
        deferredPrompt = null;
    }
});

window.addEventListener('appinstalled', () => {
    $('installCard').style.display = 'none';
    showToast('অ্যাপটি সফলভাবে ইন্সটল হয়েছে');
});

// --- Authentication ---
function showAuthMode(mode) {
    $('loginFields').style.display = mode === 'login' ? 'block' : 'none';
    $('registerFields').style.display = mode === 'register' ? 'block' : 'none';
    $('recoveryFields').style.display = mode === 'recovery' ? 'block' : 'none';
    $('authTitle').textContent = mode === 'login' ? 'লগইন' : (mode === 'register' ? 'রেজিস্ট্রেশন' : 'পিন রিসেট');
}

async function handleAuth(mode) {
    let phone, pin, security_answer;
    
    if (mode === 'login') {
        phone = $('authPhone').value.trim();
        pin = $('authPin').value.trim();
    } else if (mode === 'register') {
        phone = $('regPhone').value.trim();
        pin = $('regPin').value.trim();
        security_answer = $('regSecurity').value.trim();
    } else if (mode === 'recovery') {
        phone = $('recPhone').value.trim();
        pin = $('recNewPin').value.trim();
        security_answer = $('recSecurity').value.trim();
    }

    if (!phone || pin.length < 4 || (mode !== 'login' && !security_answer)) {
        showToast('সবগুলো ঘর সঠিকভাবে পূরণ করুন');
        return;
    }

    const formData = new FormData();
    formData.append('phone', phone);
    formData.append('pin', pin);
    formData.append('mode', mode);
    if (security_answer) formData.append('security_answer', security_answer);

    try {
        const res = await fetch(API + '?action=auth', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            if (mode === 'recovery') {
                showToast('পিন রিসেট সফল হয়েছে, এখন লগইন করুন');
                showAuthMode('login');
                return;
            }

            userProfile.user_id = data.user_id;
            userProfile.phone = phone;
            userProfile.name = data.name;
            localStorage.setItem('userProfile', JSON.stringify(userProfile));
            
            $('authOverlay').style.display = 'none';
            showToast(data.new ? 'রেজিস্ট্রেশন সফল হয়েছে' : 'লগইন সফল হয়েছে');
            
            if (!data.new) {
                await syncDown();
            }
            renderProfile();
            renderMyCases();
        } else {
            showToast(data.error || 'ব্যর্থ হয়েছে');
        }
    } catch (e) {
        showToast('সার্ভার ত্রুটি');
    }
}

async function syncUp() {
    if (!userProfile.user_id) return;
    const formData = new FormData();
    formData.append('user_id', userProfile.user_id);
    formData.append('cases', JSON.stringify(myCases));
    
    try {
        await fetch(API + '?action=sync_up', {
            method: 'POST',
            body: formData
        });
    } catch (e) {
        console.error('Sync up failed', e);
    }
}

async function syncDown() {
    if (!userProfile.user_id) return;
    try {
        const res = await fetch(API + '?action=sync_down&user_id=' + userProfile.user_id);
        const data = await res.json();
        if (Array.isArray(data)) {
            myCases = data;
            localStorage.setItem('myCases', JSON.stringify(myCases));
        }
    } catch (e) {
        console.error('Sync down failed', e);
    }
}

function logout() {
    if (confirm('লগআউট করলে লোকাল ডাটা মুছে যাবে। আপনি কি নিশ্চিত?')) {
        localStorage.clear();
        location.reload();
    }
}

// --- Profile Management ---
function renderProfile() {
    $('profileNameDisplay').textContent = userProfile.name || 'ব্যবহারকারী';
    $('profilePhoneDisplay').textContent = userProfile.phone || '+৮৮০XXXXXXXXXX';
    $('inputProfileName').value = userProfile.name || '';
    $('inputProfilePhone').value = userProfile.phone || '';
    $('inputProfilePhone').disabled = true; // Phone cannot be changed
}

async function saveProfile() {
    const newName = $('inputProfileName').value.trim();
    if (!newName) return;
    
    userProfile.name = newName;
    localStorage.setItem('userProfile', JSON.stringify(userProfile));
    
    const formData = new FormData();
    formData.append('user_id', userProfile.user_id);
    formData.append('name', newName);
    
    try {
        await fetch(API + '?action=update_profile', {
            method: 'POST',
            body: formData
        });
        renderProfile();
        showToast('প্রোফাইল আপডেট হয়েছে');
    } catch (e) {
        showToast('সার্ভার আপডেট ব্যর্থ');
    }
}

function exportData() {
    const data = {
        userProfile,
        myCases,
        savedCourts,
        exportDate: new Date().toISOString()
    };
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `court_app_backup_${new Date().toISOString().split('T')[0]}.json`;
    a.click();
    URL.revokeObjectURL(url);
}

function importData(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = JSON.parse(e.target.result);
            if (confirm('রিস্টোর করলে বর্তমান সকল ডাটা মুছে যাবে। আপনি কি নিশ্চিত?')) {
                if (data.userProfile) {
                    userProfile = data.userProfile;
                    localStorage.setItem('userProfile', JSON.stringify(userProfile));
                }
                if (data.myCases) {
                    myCases = data.myCases;
                    localStorage.setItem('myCases', JSON.stringify(myCases));
                }
                if (data.savedCourts) {
                    savedCourts = data.savedCourts;
                    localStorage.setItem('savedCourts', JSON.stringify(savedCourts));
                }
                renderProfile();
                renderMyCases();
                renderSavedCourts();
                showToast('ডাটা সফলভাবে রিস্টোর হয়েছে');
            }
        } catch (err) {
            showToast('ফাইলটি সঠিক নয়');
        }
    };
    reader.readAsText(file);
}

// --- My Cases Management ---
function addCaseManually() {
    const caseNo = en2bn($('manualCaseNo').value.trim());
    let courtId = $('manualLowerCourt').value;
    let courtName = '';
    
    if (courtId) {
        const courtSelect = $('manualLowerCourt');
        courtName = courtSelect.options[courtSelect.selectedIndex].text;
    } else {
        courtName = $('manualCourtName').value.trim();
        courtId = 0;
    }
    
    const nextDate = en2bn($('manualNextDate').value.trim());
    const plaintiff = $('manualPlaintiff').value.trim();
    const defendant = $('manualDefendant').value.trim();
    const pdfFile = $('manualPdf').files[0];

    if (!caseNo || !courtName) {
        showToast('মামলা নম্বর এবং আদালতের নাম আবশ্যক');
        return;
    }

    const processAddition = (pdfData = null, pdfName = '') => {
        const newCase = {
            courtId: parseInt(courtId) || 0,
            courtName: courtName,
            case_no: caseNo,
            plaintiff: plaintiff,
            defendant: defendant,
            pdfData: pdfData,
            pdfName: pdfName,
            last_activity: 'ম্যানুয়ালি যোগ করা',
            prev_date: '',
            next_date: nextDate,
            last_order: '',
            history: [{
                date: new Date().toISOString().split('T')[0],
                activity: 'ম্যানুয়ালি যোগ করা',
                next_date: nextDate
            }],
            last_checked: new Date().toISOString()
        };

        myCases.push(newCase);
        localStorage.setItem('myCases', JSON.stringify(myCases));
        showToast('মামলাটি যোগ করা হয়েছে' + (courtId > 0 ? ' (অটো-আপডেট সক্রিয়)' : ''));
        syncUp();
        
        // Clear fields
        $('manualCaseNo').value = '';
        $('manualCourtName').value = '';
        $('manualNextDate').value = '';
        $('manualPlaintiff').value = '';
        $('manualDefendant').value = '';
        $('manualPdf').value = '';
        $('manualDivision').value = '';
        $('manualDistrict').innerHTML = '<option value="">জেলা</option>';
        $('manualDistrict').disabled = true;
        $('manualCourtLayer').innerHTML = '<option value="">আদালতের ধরন</option>';
        $('manualCourtLayer').disabled = true;
        $('manualLowerCourt').innerHTML = '<option value="">আদালত</option>';
        $('manualLowerCourt').disabled = true;
        
        renderMyCases();
    };

    if (pdfFile) {
        if (pdfFile.size > 2 * 1024 * 1024) {
            showToast('পিডিএফ সাইজ ২ মেগাবাইটের বেশি হওয়া যাবে না');
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            processAddition(e.target.result, pdfFile.name);
        };
        reader.readAsDataURL(pdfFile);
    } else {
        processAddition();
    }
}

function addToMyCases(courtId, courtName, caseData) {
    const caseNo = en2bn(caseData.case_no);
    if (myCases.find(c => c.courtId === courtId && c.case_no === caseNo)) {
        showToast('মামলাটি ইতিমধ্যে যোগ করা হয়েছে');
        return;
    }
    const newCase = {
        courtId,
        courtName,
        case_no: caseNo,
        last_activity: caseData.activity,
        prev_date: caseData.prev_date || '', // Store previous date
        next_date: caseData.next_date,
        last_order: caseData.order,
        history: [{
            date: new Date().toISOString().split('T')[0],
            activity: caseData.activity,
            next_date: caseData.next_date
        }],
        last_checked: new Date().toISOString()
    };
    myCases.push(newCase);
    localStorage.setItem('myCases', JSON.stringify(myCases));
    showToast('মামলাটি "আমার মামলা" তালিকায় যোগ করা হয়েছে');
    renderMyCases();
    syncUp();
}

function deleteCase(courtId, caseNo) {
    myCases = myCases.filter(c => !(c.courtId === courtId && c.case_no === caseNo));
    localStorage.setItem('myCases', JSON.stringify(myCases));
    renderMyCases();
    showToast('মামলাটি মুছে ফেলা হয়েছে');
    syncUp();
}

function renderMyCases() {
    const container = $('myCasesList');
    const upcomingContainer = $('upcomingCasesList');
    const upcomingSection = $('upcomingCasesSection');
    
    if (myCases.length === 0) {
        upcomingSection.style.display = 'none';
        container.innerHTML = `
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="var(--text-secondary)"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                <h3>কোনো মামলা যোগ করা নেই</h3>
                <p>কার্যতালিকা বা অনুসন্ধান থেকে আপনার মামলা যোগ করুন</p>
            </div>`;
        return;
    }

    // Sort cases by next date if possible
    const sortedCases = [...myCases].sort((a, b) => {
        if (!a.next_date) return 1;
        if (!b.next_date) return -1;
        return 0; // Keeping it simple for now as formats vary
    });

    // Determine tomorrow's date string in DD-MM-YYYY
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const tomorrowStr = formatDateToApi(tomorrow);
    const tomorrowStrBn = en2bn(tomorrowStr);

    let html = '';
    let upcomingHtml = '';
    let hasUpcoming = false;

    for (const c of sortedCases) {
        const history = c.history || [];
        const prevEntry = history.length > 1 ? history[history.length - 2] : null;
        
        const isTomorrow = c.next_date && (c.next_date.includes(tomorrowStr) || c.next_date.includes(tomorrowStrBn));
        
        const caseItemHtml = `
            <div class="case-item" style="border-left-color: ${isTomorrow ? '#d32f2f' : 'var(--accent)'}; margin-bottom:12px; ${isTomorrow ? 'background: #fff8f8; border-width: 2px;' : ''}">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div class="case-no" style="color:var(--primary)">${c.case_no}</div>
                    <button class="delete-btn" onclick="deleteCase(${c.courtId}, '${c.case_no.replace(/'/g, "\\'")}')">✕</button>
                </div>
                <div style="font-size:12px; color:var(--text-secondary); margin-bottom:6px;">${c.courtName}</div>
                <div class="case-activity">${c.last_activity}</div>
                
                ${c.plaintiff || c.defendant ? `
                <div style="background:rgba(0,0,0,0.02); padding:8px; border-radius:6px; margin-top:8px; font-size:12px;">
                    ${c.plaintiff ? `<div><span style="font-weight:600;color:var(--primary)">বাদী:</span> ${c.plaintiff}</div>` : ''}
                    ${c.defendant ? `<div><span style="font-weight:600;color:var(--danger)">বিবাদী:</span> ${c.defendant}</div>` : ''}
                </div>
                ` : ''}

                ${c.pdfData ? `
                <div style="margin-top:8px;">
                    <a href="${c.pdfData}" download="${c.pdfName || 'case_document.pdf'}" class="btn btn-sm btn-outline" style="font-size:11px; padding:4px 8px; width:auto; border-color:#e53935; color:#e53935;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#e53935" style="margin-right:4px"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9h1v1H9V9zm5.5 1.5h1v1h-1v-1z"/></svg>PDF দেখুন/ডাউনলোড
                    </a>
                </div>
                ` : ''}
                
                <div style="display:flex; gap:10px; margin-top:8px; font-size:12px;">
                    <div style="flex:1; background:rgba(0,0,0,0.04); padding:6px; border-radius:6px;">
                        <div style="color:var(--text-secondary); font-size:10px;">আগের তারিখ</div>
                        <div style="font-weight:600;">${prevEntry ? formatDateBn(prevEntry.next_date) : (c.prev_date || 'N/A')}</div>
                    </div>
                    <div style="flex:1; background:rgba(46,125,50,0.1); padding:6px; border-radius:6px;">
                        <div style="color:var(--success); font-size:10px;">পরবর্তী তারিখ ${isTomorrow ? '(আগামীকাল)' : ''}</div>
                        <div style="font-weight:700; color:${isTomorrow ? '#d32f2f' : 'var(--success)'};">${c.next_date ? formatDateBn(c.next_date) : 'আপলোড হয়নি'}</div>
                    </div>
                </div>

                <div style="font-size:11px; color:var(--text-secondary); margin-top:8px;">সর্বশেষ চেক: ${new Date(c.last_checked).toLocaleTimeString()}</div>
                <button class="btn btn-sm btn-outline" style="margin-top:8px; font-size:12px; padding:4px 8px; width:auto;" onclick="${c.courtId ? `syncCase(${c.courtId}, '${c.case_no.replace(/'/g, "\\'")}')` : `showToast('ম্যানুয়াল মামলার অটো-আপডেট সম্ভব নয়')`}">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="var(--primary)" style="margin-right:4px"><path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm-1.25 11.4l-1.46-1.46C8.46 12.97 8 11.43 8 10c0-4.42 3.58-8 8-8v3l4-4-4-4v3c-3.31 0-6 2.69-6 6 0 1.01.25 1.97.7 2.8l-1.46-1.46z"/></svg>আপডেট করুন
                </button>
            </div>`;
            
        if (isTomorrow) {
            upcomingHtml += caseItemHtml;
            hasUpcoming = true;
        }
        html += caseItemHtml;
    }
    
    upcomingSection.style.display = hasUpcoming ? 'block' : 'none';
    upcomingContainer.innerHTML = upcomingHtml;
    container.innerHTML = html;
}

function formatDateToApi(date) {
    const d = date.getDate();
    const m = date.getMonth() + 1;
    const y = date.getFullYear();
    return (d < 10 ? '0' + d : d) + '-' + (m < 10 ? '0' + m : m) + '-' + y;
}

async function syncCase(courtId, caseNo) {
    showToast('আপডেট চেক করা হচ্ছে...');
    try {
        const res = await fetch(API + '?action=caseinfo&courtId=' + courtId + '&caseno=' + encodeURIComponent(caseNo));
        const data = await res.json();
        
        if (data.cases && data.cases.length > 0) {
            const remoteCase = data.cases[0];
            const idx = myCases.findIndex(c => c.courtId === courtId && c.case_no === caseNo);
            
            if (idx > -1) {
                const oldDate = myCases[idx].next_date;
                myCases[idx].last_activity = remoteCase.activity;
                myCases[idx].next_date = remoteCase.next_date;
                myCases[idx].last_order = remoteCase.order;
                myCases[idx].last_checked = new Date().toISOString();
                
                if (oldDate !== remoteCase.next_date && remoteCase.next_date) {
                    if (!myCases[idx].history) myCases[idx].history = [];
                    myCases[idx].history.push({
                        date: new Date().toISOString().split('T')[0],
                        activity: remoteCase.activity,
                        next_date: remoteCase.next_date
                    });
                    notifyNewDate(caseNo, remoteCase.next_date);
                }
                
                localStorage.setItem('myCases', JSON.stringify(myCases));
                renderMyCases();
                syncUp();
                showToast('আপডেট সম্পন্ন হয়েছে');
            }
        }
    } catch (e) {
        console.error(e);
        showToast('আপডেট ব্যর্থ হয়েছে');
    }
}

function notifyNewDate(caseNo, newDate) {
    if (Notification.permission === "granted") {
        new Notification("মামলার নতুন তারিখ!", {
            body: `মামলা নং ${caseNo} এর পরবর্তী তারিখ: ${newDate}`,
            icon: "icon-192.png"
        });
    } else {
        showToast(`নতুন তারিখ: ${newDate} (মামলা: ${caseNo})`);
    }
}

// --- Setup Home Page Dropdowns ---
function setupDropdowns(divId, distId, layerId, courtId, searchBtnId) {
    const divSel = $(divId);
    const distSel = $(distId);
    const layerSel = $(layerId);
    const courtSel = $(courtId);
    const searchBtn = searchBtnId ? $(searchBtnId) : null;

    divSel.addEventListener('change', () => {
        const v = divSel.value;
        if (v) {
            loadDistricts(v, distId);
            layerSel.innerHTML = '<option value="0">আদালতের ধরন</option>';
            layerSel.disabled = true;
            courtSel.innerHTML = '<option value="">আদালতের ধরন নির্বাচন করুন</option>';
            courtSel.disabled = true;
            if (searchBtn) searchBtn.disabled = true;
        }
    });

    distSel.addEventListener('change', () => {
        const v = distSel.value;
        if (v) {
            setupCourtLayer(v, layerId);
            courtSel.innerHTML = '<option value="">আদালতের ধরন নির্বাচন করুন</option>';
            courtSel.disabled = true;
            if (searchBtn) searchBtn.disabled = true;
        }
    });

    layerSel.addEventListener('change', () => {
        const distVal = distSel.value;
        const oriVal = layerSel.value;
        const dId = distVal.split('-')[0];
        if (dId && oriVal && oriVal !== '0') {
            loadLowerCourts(dId, oriVal, courtId);
            if (searchBtn) searchBtn.disabled = true;
        }
    });

    courtSel.addEventListener('change', () => {
        if (searchBtn) searchBtn.disabled = !courtSel.value;
    });
}

// --- Init ---
document.addEventListener('DOMContentLoaded', () => {
    setupQuickDates();
    setupDropdowns('division', 'district', 'courtLayer', 'lowerCourt', 'btnSearch');
    setupDropdowns('searchDivision', 'searchDistrict', 'searchCourtLayer', 'searchLowerCourt', 'btnCaseSearch');

    loadDivisions('division');
    loadDivisions('searchDivision');
    loadDivisions('manualDivision');

    setupDropdowns('manualDivision', 'manualDistrict', 'manualCourtLayer', 'manualLowerCourt', null);

    // Initial Auth Check
    if (userProfile.user_id) {
        $('authOverlay').style.display = 'none';
        renderProfile();
        renderMyCases();
    }

    // Home search button
    $('btnSearch').addEventListener('click', () => {
        const courtId = $('lowerCourt').value;
        const date = $('caseDate').value;
        if (!courtId) { showToast('আদালত নির্বাচন করুন'); return; }
        fetchCauselist(courtId, date, 'causelistResult');
    });

    // Case search button
    $('btnCaseSearch').addEventListener('click', () => {
        const courtId = $('searchLowerCourt').value;
        const caseno = $('searchCaseNo').value;
        if (!courtId) { showToast('আদালত নির্বাচন করুন'); return; }
        if (!caseno) { showToast('মামলার নম্বর লিখুন'); return; }
        // Manual case add
    const caseNo = $('searchCaseNo').value;
    if (caseNo) {
        const normalizedCaseNo = en2bn(caseNo);
        $('searchCaseNo').value = normalizedCaseNo;
    }
    fetchCaseInfo(courtId, normalizedCaseNo, 'caseSearchResult');
    });

    renderSavedCourts();
    renderMyCases();
    renderProfile();
    
    // Request notification permission
    if ("Notification" in window) {
        Notification.requestPermission();
    }
    
    // Auto sync every 30 minutes if page is open
    setInterval(async () => {
        for (const c of myCases) {
            if (c.courtId) {
                const oldDate = c.next_date;
                await syncCase(c.courtId, c.case_no);
                // The syncCase already updates myCases and calls syncUp()
            }
        }
    }, 30 * 60 * 1000);
});

// Service Worker Registration
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(() => {});
}
</script>
</body>
</html>
