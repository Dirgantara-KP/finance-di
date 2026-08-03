<h2
    class="fi-topbar-page-title truncate text-base font-semibold text-gray-950 dark:text-white"
    style="margin-inline-start: 1rem;"
    x-data
    x-init="
        const syncTopbarTitle = () => {
            const heading = document.querySelector('.fi-header-heading');
            $el.textContent = heading ? heading.textContent.trim() : '';
        };
        syncTopbarTitle();
        document.addEventListener('livewire:navigated', syncTopbarTitle);
    "
></h2>