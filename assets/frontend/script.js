let queryDict = {}
location.search.substr(1).split("&").forEach(function (item) {
    queryDict[item.split("=")[0]] = item.split("=")[1]
})

function tabs() {
    let allTabs = document.querySelectorAll('.tabs li');
    let allTabsContents = document.querySelectorAll('.tabs-content');

    let initialUrlTabId = location.hash.substring(1);
    activateTab(initialUrlTabId);

    allTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            activateTab(tab.dataset.tabId);
        })
    })

    function activateTab(id) {
        let exists = [...document.querySelectorAll('.tabs li')].filter(tab => tab.dataset.tabId == id).length;
        if (!exists) return;

        allTabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.dataset.tabId == id) {
                tab.classList.add('active');
            }
        })

        allTabsContents.forEach(t => {
            t.classList.remove('active');
            if (t.dataset.tabId == id) {
                t.classList.add('active');
            }
        })

        location.hash = id;
    }
}

document.addEventListener("DOMContentLoaded", function () {
    tabs();
})