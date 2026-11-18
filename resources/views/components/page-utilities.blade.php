@props(['showLogout' => true])

<div class="page-utilities" data-page-utilities>
    @if ($showLogout)
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="page-utilities__button page-utilities__button--logout" aria-label="ログアウト">
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                    </svg>
                    <span>ログアウト</span>
                </button>
            </form>
        @endauth
    @endif

    <button type="button" class="page-utilities__button page-utilities__button--top" data-scroll-to-top hidden aria-label="ページ上部へ戻る">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 15l6-6 6 6" />
        </svg>
        <span>上へ</span>
    </button>
</div>
