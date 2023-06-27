<header>
        <section>
            <!-- MAIN CONTAINER -->
            <div id="container">
                <!-- SHOP NAME -->
                <div id="shopName"><a href="{{ url('/') }}"> {{ config('app.name', 'Laravel') }} </a></div>
                    <!-- COLLCETIONS ON WEBSITE -->
                    <div id="collection">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                All Category
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                
                                <a class="dropdown-item" href="{{ url('/')}}">All</a>
                                @foreach ($categoryList as $category)
                                    <a class="dropdown-item" href="{{url('?cpid=').$category->id}}">{{$category->name}}</a>
                                @endforeach
                            </li>
                        </ul>
                    </div>
                    <!-- SEARCH SECTION -->
                    <div id="search">
                       <form action="{{ url('/')}}" method="get">
                            <i class="fas fa-search search"></i>
                            <input type="text" id="input" name="q" placeholder="Search for Book">
                       </form>
                    </div>
                    <!-- USER SECTION (CART AND USER ICON) -->
                    <div id="user">
                        <a href="{{url('/get/cart')}}" id="cartQty"> <i class="fas fa-shopping-cart addedToCart"><div id="badge"> 0 </div></i></a>
                    </div>
            </div>

        </section>
    </header>