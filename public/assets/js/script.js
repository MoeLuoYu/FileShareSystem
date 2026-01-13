document.addEventListener('DOMContentLoaded', function() {
    const searchToggle = document.getElementById('searchToggle');
    const searchForm = document.getElementById('searchForm');

    if (searchToggle && searchForm) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchForm.classList.toggle('active');
        });

        // 点击页面其他地方关闭搜索框
        document.addEventListener('click', function(e) {
            if (!searchToggle.contains(e.target) && !searchForm.contains(e.target)) {
                searchForm.classList.remove('active');
            }
        });
    }

    // 动态调整主内容区域的底部边距，以适应footer的高度
    function adjustContentPadding() {
        const footer = document.querySelector('footer');
        const mainWrapper = document.querySelector('.main-content-wrapper');

        if (footer && mainWrapper) {
            const footerHeight = footer.offsetHeight;
            mainWrapper.style.paddingBottom = (footerHeight + 20) + 'px'; // 加20px额外间距
        }
    }

    // 初始调整
    adjustContentPadding();

    // 监听窗口大小改变事件，重新调整
    window.addEventListener('resize', adjustContentPadding);

    // 监听DOM变化，如果footer内容发生变化则重新调整
    const observer = new MutationObserver(function(mutations) {
        let shouldAdjust = false;
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                // 检查是否影响了footer
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node === document.querySelector('footer') || node.closest('footer')) {
                            shouldAdjust = true;
                        }
                    }
                });
                mutation.removedNodes.forEach(function(node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node === document.querySelector('footer') || node.closest('footer')) {
                            shouldAdjust = true;
                        }
                    }
                });
            }
        });

        if (shouldAdjust) {
            setTimeout(adjustContentPadding, 100); // 延迟执行以确保DOM完全更新
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class']
    });
});