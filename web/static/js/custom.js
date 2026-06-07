// 等待页面加载完成
document.addEventListener('DOMContentLoaded', function() {

   
    // 添加永久网址显示
    function addPermanentUrl() {
        const rbox = document.querySelector('.rbox');
        if (!rbox) {
            
            setTimeout(addPermanentUrl, 1000);
            return;
        }

        // 创建永久网址显示元素
        const urlDiv = document.createElement('div');
        urlDiv.className = 'permanent-url';
        urlDiv.innerHTML = `<span>永久网址：${window.location.host}🔍</span>`;
        
        // 将元素插入到rbox之前
        rbox.parentNode.insertBefore(urlDiv, rbox);
    }

    // 确保在页面完全加载后执行
    window.addEventListener('load', function() {
        setTimeout(addPermanentUrl, 1000);
    });

    // 动态添加样式
    function addStyle(styleString) {
        const style = document.createElement('style');
        style.textContent = styleString;
        document.head.append(style);
    }

    // 添加自定义样式
    addStyle(`
        /* 自定义样式 */
        #redPacket {
            transition: all 0.3s ease;
        }
        
        #redPacket:hover {
            transform: scale(1.1);
        }
        
        /* 添加阴影效果 */
        #redPacket {
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* 添加点击效果 */
        #redPacket:active {
            transform: scale(0.95);
        }
    `);

    // 动态修改红包位置
    const redPacket = document.getElementById('redPacket');
    if (redPacket) {
        // 监听窗口大小变化
        window.addEventListener('resize', function() {
            const windowWidth = window.innerWidth;
            if (windowWidth < 768) {
                redPacket.style.right = '10px';
                redPacket.style.bottom = '50px';
            } else {
                redPacket.style.right = '20px';
                redPacket.style.bottom = '80px';
            }
        });
    }

    // 轮播自动播放和特效
    function autoSwipe() {
        var swipe = document.querySelector('.van-swipe__track');
        var items = document.querySelectorAll('.van-swipe-item');
        if (!swipe || items.length === 0) return;
        
        let current = 0;
        const total = items.length;
        
        function updateDots() {
            const dots = document.querySelector('.swiper-dots');
            if (dots) {
                const num = dots.querySelector('.num:first-child');
                if (num) {
                    num.textContent = current + 1;
                }
            }
        }

        function nextSlide() {
            current = (current + 1) % total;
            const itemWidth = items[0].offsetWidth;
            
            // 添加过渡动画
            swipe.style.transition = 'transform 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            swipe.style.transform = `translateX(${-itemWidth * current}px)`;
            
            // 更新激活状态
            items.forEach((item, idx) => {
                if (idx === current) {
                    item.classList.add('swipe-active');
                } else {
                    item.classList.remove('swipe-active');
                }
            });
            
            updateDots();
        }

        // 初始化第一个slide
        items[0].classList.add('swipe-active');
        updateDots();

        // 自动播放
        setInterval(nextSlide, 3000);
    }

    // 延迟执行，确保DOM完全加载
    setTimeout(autoSwipe, 1000);

    console.log('页面初始化完成');
    // 开始检查分类内容
    waitForContent();
});

// 等待分类内容加载完成
function waitForContent() {
    console.log('等待分类内容加载...');
    const checkContent = () => {
        const containers = document.querySelectorAll('.rigts');
        if (containers.length > 0) {
            console.log('分类内容已加载，找到容器数量:', containers.length);
            // 内容已加载，添加热度标签
            addHotTagToRigtsImages();
            // 设置分类切换监听
            setupCategoryListeners();
        } else {
            console.log('分类内容未加载，继续等待...');
            // 继续等待
            setTimeout(checkContent, 100);
        }
    };
    checkContent();
}

// 设置分类切换监听
function setupCategoryListeners() {
    // 监听分类切换
    document.body.addEventListener('click', function(e) {
        const target = e.target;
        if (target.classList.contains('ls') || target.closest('.ls')) {
            console.log('分类被点击');
            // 等待分类切换动画完成
            setTimeout(() => {
                console.log('分类切换完成，更新热度标签');
                addHotTagToRigtsImages();
            }, 300);
        }
    });

    // 监听滚动和调整大小
    window.addEventListener('scroll', throttle(updateTagPositions, 100));
    window.addEventListener('resize', throttle(updateTagPositions, 100));
}

function addHotTagToRigtsImages() {
    console.log('开始添加热度标签');
    const containers = document.querySelectorAll('.rigts');
    console.log('找到容器数量:', containers.length);
    
    containers.forEach(function(container, containerIndex) {
        console.log(`处理第 ${containerIndex + 1} 个容器`);
        
        // 移除旧的标签
        const oldTags = container.querySelectorAll('.hot-tag');
        console.log(`移除 ${oldTags.length} 个旧标签`);
        oldTags.forEach(tag => tag.remove());

        // 添加新标签
        const imgs = container.querySelectorAll('img');
        console.log(`找到 ${imgs.length} 个图片元素`);
        
        imgs.forEach(function(img, imgIndex) {
            console.log(`处理第 ${imgIndex + 1} 个图片`);
            console.log('图片尺寸:', img.offsetWidth, 'x', img.offsetHeight);
            console.log('图片位置:', img.getBoundingClientRect());
            
            const rand = Math.floor(Math.random() * (1555 - 155 + 1)) + 155;
            const tag = document.createElement('div');
            tag.className = 'hot-tag';
            tag.innerHTML = `<span class="hot-num">${rand}</span><span class="hot-text">热度</span>`;
            
            // 设置标签样式
            const imgRect = img.getBoundingClientRect();
            const parentRect = container.getBoundingClientRect();
            const left = imgRect.left - parentRect.left + 30;
            const top = imgRect.top - parentRect.top + img.offsetHeight * 0.6;
            
            console.log('标签位置:', { left, top });
            
            Object.assign(tag.style, {
                position: 'absolute',
                left: left + 'px',
                top: top + 'px',
                transform: 'translateY(-50%)',
                background: 'none',
                color: '#303442',
                fontFamily: "'AkrobatBold', 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', Arial, sans-serif",
                fontWeight: 'bold',
                padding: '0',
                borderRadius: '0',
                zIndex: '2',
                pointerEvents: 'none',
                boxShadow: 'none',
                display: 'flex',
                alignItems: 'flex-end',
                gap: '2px'
            });
            
            container.appendChild(tag);
            console.log('标签已添加到容器');
        });
    });
}

function updateTagPositions() {
    const containers = document.querySelectorAll('.rigts');
    containers.forEach(function(container) {
        const imgs = container.querySelectorAll('img');
        const tags = container.querySelectorAll('.hot-tag');
        
        imgs.forEach(function(img, index) {
            if (tags[index]) {
                const imgRect = img.getBoundingClientRect();
                const parentRect = container.getBoundingClientRect();
                const left = imgRect.left - parentRect.left + 30;
                const top = imgRect.top - parentRect.top + img.offsetHeight * 0.6;
                
                tags[index].style.left = left + 'px';
                tags[index].style.top = top + 'px';
            }
        });
    });
}

/**
 * 工具函数：节流
 * @param {Function} func 要执行的函数
 * @param {number} limit 时间限制（毫秒）
 * @returns {Function} 节流后的函数
 */
function throttle(func, limit) {
    let inThrottle;
    return function executedFunction(...args) {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// 导出工具函数
window.utils = {
    throttle
};