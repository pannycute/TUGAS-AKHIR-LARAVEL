<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Sistem Order</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .products-container {
            padding: 60px 0;
            background: #f8f9fa;
        }
        
        .product-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 30px;
            border: none;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .product-image {
            height: 250px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        
        .product-body {
            padding: 25px;
        }
        
        .product-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }
        
        .product-description {
            color: #718096;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .product-duration {
            background: #e2e8f0;
            color: #4a5568;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .btn-order {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stats-label {
            color: #718096;
            font-weight: 500;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .search-input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 1.1rem;
        }
        
        .search-input::placeholder {
            color: #a0aec0;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .product-card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/" style="color: #667eea;">
                <i class="fas fa-shopping-cart me-2"></i>
                Sistem Order
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="hero-title floating">Our Amazing Products</h1>
                    <p class="hero-subtitle">Discover our collection of high-quality products designed to meet your needs</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-container">
        <div class="container">
            <!-- Stats Row -->
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="stats-card fade-in">
                        <div class="stats-number">{{ $products->count() }}</div>
                        <div class="stats-label">Total Products</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card fade-in" style="animation-delay: 0.2s;">
                        <div class="stats-number">{{ $products->where('duration', '1 month')->count() }}</div>
                        <div class="stats-label">Monthly Plans</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card fade-in" style="animation-delay: 0.4s;">
                        <div class="stats-number">{{ $products->where('duration', '1 week')->count() }}</div>
                        <div class="stats-label">Weekly Plans</div>
                    </div>
                </div>
            </div>

            <!-- Search Box -->
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="search-box">
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-transparent">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control search-input" id="searchInput" placeholder="Search products...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="productsGrid">
                @foreach($products as $index => $product)
                <div class="col-lg-4 col-md-6 product-item" data-name="{{ strtolower($product->name) }}" data-description="{{ strtolower($product->description) }}">
                    <div class="product-card fade-in" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="product-image">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-body">
                            <h5 class="product-title">{{ $product->name }}</h5>
                            <p class="product-description">{{ $product->description }}</p>
                            <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <div class="product-duration">
                                <i class="fas fa-clock me-2"></i>
                                {{ $product->duration }}
                            </div>
                            <button class="btn btn-order">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Order Now
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Empty State -->
            @if($products->count() == 0)
            <div class="row">
                <div class="col-lg-6 mx-auto text-center">
                    <div class="fade-in">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: #cbd5e0; margin-bottom: 20px;"></i>
                        <h3 style="color: #718096; margin-bottom: 10px;">No Products Available</h3>
                        <p style="color: #a0aec0;">Check back later for new products!</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; 2024 Sistem Order. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Search Functionality -->
    <script>
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');
            
            productItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const description = item.getAttribute('data-description');
                
                if (name.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.3s ease-in';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 