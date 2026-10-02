<style>
    html, body.login-page-body {
        height: 100%;
        margin: 0;
        overflow: hidden;
        background: #f8fafc;
    }
    body.login-page-body .overlay,
    body.login-page-body .pace {
        display: none !important;
    }
    .login-split {
        display: flex;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        overflow: hidden;
    }
    .login-hero {
        position: relative;
        flex: 0 0 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        background: transparent;
    }
    .login-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .login-panel {
        flex: 0 0 50%;
        width: 50%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow-y: auto;
        background: #f8fafc;
        padding: 2rem 1.5rem;
    }
    .login-lang {
        position: absolute;
        top: 1rem;
        right: 1.25rem;
        z-index: 20;
    }
    .login-lang summary {
        color: #334155 !important;
        cursor: pointer;
        list-style: none;
    }
    .login-lang summary::-webkit-details-marker {
        display: none;
    }
    .login-card {
        width: 100%;
        max-width: 26rem;
        background: #fff;
        border-radius: 1.25rem;
        padding: 2rem 1.75rem;
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid #e2e8f0;
    }
    .login-brand {
        display: flex;
        justify-content: center;
        margin: 0 0 1.5rem;
        padding: 0.1rem 0 1.4rem;
        border-bottom: 1px solid #eef2f7;
    }
    .login-brand-lockup {
        text-align: center;
        max-width: 18.5rem;
    }
    .login-brand h1 {
        margin: 0;
        color: var(--login-theme);
        font-size: 3.1rem;
        line-height: 1;
        font-weight: 800;
        letter-spacing: 0.06em;
    }
    .login-brand p {
        margin: 0.55rem 0 0;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 500;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        line-height: 1.35;
        white-space: nowrap;
    }
    .login-form-title {
        margin: 0 0 1rem;
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
        text-align: left;
    }
    .login-card input[type="text"],
    .login-card input[type="password"],
    .login-card input[type="email"] {
        width: 100%;
        border: 1px solid #d1d5da;
        outline: none;
        height: 3rem;
        background: transparent;
        border-radius: 0.75rem;
        padding: 0 0.75rem;
        font-weight: 500;
        color: #111;
    }
    .login-card input:focus {
        border-color: var(--login-theme);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--login-theme) 22%, transparent);
    }
    .login-submit {
        width: 100%;
        height: 3rem;
        border: 0;
        border-radius: 0.75rem;
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        background: var(--login-theme);
        margin-top: 0.5rem;
        transition: filter 0.15s ease, transform 0.15s ease;
    }
    .login-submit:hover,
    .login-submit:focus {
        filter: brightness(0.92);
        outline: none;
    }
    .login-link-accent {
        color: var(--login-theme);
        font-weight: 600;
    }
    .login-link-accent:hover {
        color: var(--login-theme);
        text-decoration: underline;
    }
    .login-card .form-group {
        margin-bottom: 0.75rem;
    }
    .login-card .alert {
        margin-bottom: 1rem;
    }
    .login-footer-link {
        margin-top: 1rem;
        text-align: center;
    }
    @media (max-width: 991px) {
        html, body.login-page-body {
            overflow: auto;
        }
        .login-split {
            flex-direction: column;
            height: auto;
            min-height: 100vh;
            min-height: 100dvh;
        }
        .login-hero,
        .login-panel {
            flex: none;
            width: 100%;
        }
        .login-hero {
            height: 38vh;
            min-height: 200px;
            max-height: 280px;
        }
        .login-panel {
            height: auto;
            min-height: 68vh;
            padding: 1.25rem 1rem 2rem;
        }
        .login-lang {
            top: 0.5rem;
            right: 0.75rem;
        }
        .login-card {
            padding: 1.5rem 1.15rem;
            box-shadow: none;
            border: 0;
            background: transparent;
        }
        .login-brand {
            margin-bottom: 1.25rem;
            padding-bottom: 1.15rem;
        }
        .login-brand h1 {
            font-size: 2.35rem;
        }
        .login-brand p {
            letter-spacing: 0.08em;
            white-space: normal;
        }
    }
</style>
