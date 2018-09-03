function tabs() {
    document.addEventListener("DOMContentLoaded", function () {
        let allTabs = document.querySelectorAll('.tabs li');
        let allTabsContents = document.querySelectorAll('.tabs-content');

        allTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                allTabs.forEach(t => {
                    t.classList.remove('active');
                })
                tab.classList.add('active');

                allTabsContents.forEach(t => {
                    t.classList.remove('active');
                    if (t.dataset.tabId == tab.dataset.tabId) {
                        t.classList.add('active');
                    }
                })
            })

        })

        console.log(document.querySelectorAll('.tabs li'));
    });

}

tabs();