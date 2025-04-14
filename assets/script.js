function Navigation() {
  var nav = document.getElementById('nav');
  if (nav.className === "topnav") {
    nav.className += " responsive";
  } else {
    nav.className = "topnav";
  }
}

// Function to open order modal
function openOrderModal(supplierId = '') {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'orderModal';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <iframe src="order.php?popup=true${supplierId ? '&supplier_id='+supplierId : ''}" 
                            frameborder="0" style="width:100%; min-height:400px;"></iframe>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal on close
    modal.addEventListener('hidden.bs.modal', function() {
        modal.remove();
    });
}

// Function to close order modal
function closeOrderModal() {
    const modal = document.getElementById('orderModal');
    if(modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
    }
}

// Called when order modal is loaded
function orderModalLoaded() {
    console.log('Order modal loaded');
}