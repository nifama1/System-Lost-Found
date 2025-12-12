        </div>
    </div>

    <!-- Modal for item details -->
    <div id="item-modal" class="item-modal">
        <div class="item-modal-content">
            <span class="item-modal-close" id="item-modal-close">&times;</span>
            <img id="modal-item-image" class="modal-item-image" src="" alt="Item Image">
            <h3 id="modal-item-name"></h3>
            <p id="modal-item-description"></p>
            <div class="modal-item-meta">
                <span id="modal-item-status"></span>
                <span id="modal-item-date"></span>
                <span id="modal-item-reporter"></span>
                <span id="modal-item-phone"></span>
            </div>
            <div id="modal-item-reward" class="modal-item-reward"></div>
        </div>
    </div>

    <script>
        // User dropdown functionality
        const userInfo = document.querySelector('.user-info');
        const userDropdownUp = document.querySelector('.user-dropdown-up');

        if (userInfo && userDropdownUp) {
            userInfo.addEventListener('click', () => {
                userDropdownUp.classList.toggle('show');
            });
            document.addEventListener('click', (event) => {
                if (!userInfo.contains(event.target) && !userDropdownUp.contains(event.target)) {
                    userDropdownUp.classList.remove('show');
                }
            });
        }

        // Modal logic for item details
        function showItemDetails(item) {
            const modal = document.getElementById('item-modal');
            document.getElementById('modal-item-image').src = item.image;
            document.getElementById('modal-item-name').textContent = item.name;
            document.getElementById('modal-item-description').textContent = item.description;
            document.getElementById('modal-item-status').textContent = `Status: ${item.status}`;
            document.getElementById('modal-item-date').textContent = `Date: ${item.date}`;
            document.getElementById('modal-item-reporter').textContent = item.reporter ? `Reporter: ${item.reporter}` : '';
            document.getElementById('modal-item-phone').textContent = item.phone ? `Phone: ${item.phone}` : '';
            
            const rewardElement = document.getElementById('modal-item-reward');
            if (item.reward) {
                rewardElement.innerHTML = '<i class="fas fa-gift"></i> Reward: ' + item.reward;
                rewardElement.style.display = 'block';
            } else {
                rewardElement.style.display = 'none';
            }
            modal.style.display = 'block';
        }

        // Modal close functionality
        document.getElementById('item-modal-close').onclick = function() {
            document.getElementById('item-modal').style.display = 'none';
        };
        
        window.onclick = function(event) {
            const modal = document.getElementById('item-modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>

    <style>
        /* Add Font Awesome for icons */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

        .item-card {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 2px #4a90e2;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 22px #4a90e2;
        }

        .item-image-container {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .item-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .item-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .item-card:hover .item-overlay {
            opacity: 1;
        }

        .view-details {
            color: white;
            font-weight: 500;
            text-align: center;
            padding: 8px 16px;
            border: 2px solid white;
            border-radius: 20px;
        }

        .item-info {
            padding: 15px;
        }

        .item-info h4 {
            margin: 0 0 10px 0;
            font-size: 1.1em;
            color: #333;
        }

        .item-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .item-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .item-status.found {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .item-status.lost {
            background-color: #ffebee;
            color: #c62828;
        }

        .item-date {
            font-size: 0.85em;
            color: #666;
        }

        .item-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .item-modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 30px 20px 20px 20px;
            border-radius: 12px;
            max-width: 400px;
            position: relative;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .item-modal-close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 2em;
            color: #888;
            cursor: pointer;
            font-weight: bold;
        }
        
        .modal-item-image {
            width: 100%;
            max-width: 250px;
            height: auto;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        #modal-item-name {
            margin: 10px 0 5px 0;
            font-size: 1.3em;
            color: #333;
        }
        
        #modal-item-description {
            margin-bottom: 15px;
            color: #555;
        }
        
        .modal-item-meta {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 10px;
            font-size: 0.95em;
            color: #666;
        }
        
        .modal-item-reward {
            background: #fff3e0;
            color: #e65100;
            padding: 6px 14px;
            border-radius: 15px;
            display: inline-block;
            margin-top: 10px;
            font-size: 1em;
        }
    </style>
</body>
</html> 