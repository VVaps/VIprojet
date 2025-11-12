<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Product Details -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="md:flex">
                <div class="md:w-1/2">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                </div>
                <div class="md:w-1/2 p-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                    <p class="text-gray-600 text-lg mb-4">{{ $product->description }}</p>
                    <p class="text-2xl font-semibold text-gray-900 mb-6">{{ number_format($product->price, 2) }} €</p>
                    <button onclick="addToCart({{ $product->id }})" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Ajouter au panier
                    </button>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Commentaires</h2>
            
            <!-- Comment Form -->
            @auth
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Ajouter un commentaire</h3>
                    <form id="comment-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="parent_id" id="parent_id">
                        <div class="mb-3">
                            <textarea
                                name="content"
                                id="comment-content"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Partagez votre avis sur ce produit..."
                                required
                            ></textarea>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Publier le commentaire
                        </button>
                    </form>
                </div>
            @else
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800">
                        <a href="{{ route('login') }}" class="font-semibold hover:underline">Connectez-vous</a>
                        pour partager votre avis sur ce produit.
                    </p>
                </div>
            @endauth

            <!-- Comments List -->
            <div id="comments-container">
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="mt-2 text-gray-600">Chargement des commentaires...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const productId = {{ $product->id }};
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
        let replyingTo = null;

        // Add to cart function
        function addToCart(productId) {
            @auth
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Update cart count in navigation if available
                    if (window.Alpine && document.querySelector('[x-data]')) {
                        const navData = document.querySelector('[x-data]')._x_dataStack[0];
                        if (navData && navData.cartCount !== undefined) {
                            navData.cartCount = data.cart_count;
                        }
                    }
                } else {
                    showNotification(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showNotification('Erreur lors de l\'ajout au panier', 'error');
            });
            @else
            showNotification('Vous devez être connecté pour ajouter des produits au panier', 'error');
            @endauth
        }

        // Load comments on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadComments();
            
            // Handle comment form submission
            const commentForm = document.getElementById('comment-form');
            if (commentForm) {
                commentForm.addEventListener('submit', handleCommentSubmit);
            }
        });

        function loadComments() {
            fetch(`/products/${productId}/comments`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayComments(data.comments);
                } else {
                    console.error('Failed to load comments');
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                document.getElementById('comments-container').innerHTML =
                    '<p class="text-gray-600">Erreur lors du chargement des commentaires.</p>';
            });
        }

        function displayComments(comments) {
            const container = document.getElementById('comments-container');
            
            if (comments.length === 0) {
                container.innerHTML = '<p class="text-gray-600 text-center py-4">Aucun commentaire pour le moment. Soyez le premier à partager votre avis !</p>';
                return;
            }

            const commentsHtml = comments.map(comment => createCommentHtml(comment, false)).join('');
            container.innerHTML = commentsHtml;
        }

        function createCommentHtml(comment, isReply = false) {
            const replyButton = isAuthenticated ? `<button onclick="replyToComment(${comment.id}, '${comment.user.name}')" class="text-blue-600 hover:underline text-sm">Répondre</button>` : '';
            
            const repliesHtml = comment.replies && comment.replies.length > 0
                ? `<div class="ml-8 mt-4 space-y-4">${comment.replies.map(reply => createCommentHtml(reply, true)).join('')}</div>`
                : '';

            return `
                <div class="${isReply ? 'border-l-2 border-gray-200 pl-4' : 'border-b border-gray-200 pb-4 mb-4'}">
                    <div class="flex items-start space-x-3">
                        <div class="bg-gray-300 rounded-full h-8 w-8 flex items-center justify-center">
                            <span class="text-gray-600 font-semibold text-sm">${comment.user.name.charAt(0).toUpperCase()}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="font-semibold text-gray-900">${comment.user.name}</span>
                                <span class="text-gray-500 text-sm">${comment.created_at}</span>
                            </div>
                            <p class="text-gray-700 mb-2">${comment.content}</p>
                            ${replyButton}
                        </div>
                    </div>
                    ${repliesHtml}
                </div>
            `;
        }

        function handleCommentSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const content = formData.get('content');
            const parentId = formData.get('parent_id');

            if (!content.trim()) {
                alert('Le contenu du commentaire est requis.');
                return;
            }

            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Publication...';
            submitButton.disabled = true;

            fetch(`/products/${productId}/comments`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    content: content,
                    product_id: productId,
                    parent_id: parentId || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form
                    document.getElementById('comment-content').value = '';
                    document.getElementById('parent_id').value = '';
                    
                    // Reset reply mode if active
                    if (replyingTo) {
                        cancelReply();
                    }
                    
                    // Reload comments
                    loadComments();
                    
                    // Show success message
                    showNotification('Commentaire publié avec succès!', 'success');
                } else {
                    alert(data.message || 'Erreur lors de la publication du commentaire.');
                }
            })
            .catch(error => {
                console.error('Error posting comment:', error);
                alert('Erreur lors de la publication du commentaire.');
            })
            .finally(() => {
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        }

        function replyToComment(commentId, userName) {
            replyingTo = { id: commentId, userName: userName };
            const contentField = document.getElementById('comment-content');
            const parentIdField = document.getElementById('parent_id');
            
            contentField.placeholder = `Répondre à ${userName}...`;
            contentField.focus();
            parentIdField.value = commentId;
            
            // Show cancel reply button
            if (!document.getElementById('cancel-reply')) {
                const cancelButton = document.createElement('button');
                cancelButton.id = 'cancel-reply';
                cancelButton.type = 'button';
                cancelButton.className = 'ml-2 text-gray-600 hover:text-gray-800 text-sm';
                cancelButton.textContent = 'Annuler la réponse';
                cancelButton.onclick = cancelReply;
                contentField.parentNode.appendChild(cancelButton);
            }
        }

        function cancelReply() {
            replyingTo = null;
            const contentField = document.getElementById('comment-content');
            const parentIdField = document.getElementById('parent_id');
            const cancelButton = document.getElementById('cancel-reply');
            
            contentField.placeholder = 'Partagez votre avis sur ce produit...';
            parentIdField.value = '';
            
            if (cancelButton) {
                cancelButton.remove();
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</x-app-layout>