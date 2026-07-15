-- =============================================================================
-- Le Cameleon — Vintage Ecommerce Database Schema (PostgreSQL / Supabase)
-- Source of truth DDL documentation. Mirrored by Laravel migrations.
-- =============================================================================
-- Extensions
CREATE EXTENSION IF NOT EXISTS "pgcrypto";

-- -----------------------------------------------------------------------------
-- users — Customers, staff, and administrators
-- RLS hook: policies can scope rows by auth.uid() matching id for customers.
-- -----------------------------------------------------------------------------
CREATE TABLE users (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMPTZ,
    password        VARCHAR(255) NOT NULL,
    role            VARCHAR(20) NOT NULL DEFAULT 'customer', -- customer|staff|admin
    phone           VARCHAR(30),
    avatar_path     VARCHAR(500),
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    remember_token  VARCHAR(100),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_users_role ON users (role);
CREATE INDEX idx_users_is_active ON users (is_active);

-- -----------------------------------------------------------------------------
-- password_reset_tokens — Laravel auth
-- -----------------------------------------------------------------------------
CREATE TABLE password_reset_tokens (
    email       VARCHAR(255) PRIMARY KEY,
    token       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMPTZ
);

-- -----------------------------------------------------------------------------
-- sessions — Laravel session driver
-- -----------------------------------------------------------------------------
CREATE TABLE sessions (
    id              VARCHAR(255) PRIMARY KEY,
    user_id         UUID REFERENCES users (id) ON DELETE SET NULL,
    ip_address      VARCHAR(45),
    user_agent      TEXT,
    payload         TEXT NOT NULL,
    last_activity   INTEGER NOT NULL
);

CREATE INDEX idx_sessions_user_id ON sessions (user_id);
CREATE INDEX idx_sessions_last_activity ON sessions (last_activity);

-- -----------------------------------------------------------------------------
-- addresses — User shipping and billing addresses
-- -----------------------------------------------------------------------------
CREATE TABLE addresses (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id         UUID NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    type            VARCHAR(20) NOT NULL DEFAULT 'shipping', -- shipping|billing
    label           VARCHAR(100),
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    company         VARCHAR(150),
    line1           VARCHAR(255) NOT NULL,
    line2           VARCHAR(255),
    city            VARCHAR(100) NOT NULL,
    state           VARCHAR(100),
    postal_code     VARCHAR(20) NOT NULL,
    country         VARCHAR(2) NOT NULL DEFAULT 'US',
    phone           VARCHAR(30),
    is_default      BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_addresses_user_id ON addresses (user_id);

-- -----------------------------------------------------------------------------
-- categories — Nested taxonomy (self-referencing)
-- -----------------------------------------------------------------------------
CREATE TABLE categories (
    id              BIGSERIAL PRIMARY KEY,
    parent_id       BIGINT REFERENCES categories (id) ON DELETE SET NULL,
    name            VARCHAR(150) NOT NULL,
    slug            VARCHAR(180) NOT NULL UNIQUE,
    description     TEXT,
    image_path      VARCHAR(500),
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order      SMALLINT NOT NULL DEFAULT 0,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_categories_parent_id ON categories (parent_id);
CREATE INDEX idx_categories_is_active ON categories (is_active);

-- -----------------------------------------------------------------------------
-- brands — Designer / label directory
-- -----------------------------------------------------------------------------
CREATE TABLE brands (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    slug            VARCHAR(180) NOT NULL UNIQUE,
    description     TEXT,
    logo_path       VARCHAR(500),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- products — Vintage catalog items (apparel, objects, accessories)
-- Soft-deleted items remain for order history integrity.
-- -----------------------------------------------------------------------------
CREATE TABLE products (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    brand_id                BIGINT REFERENCES brands (id) ON DELETE SET NULL,
    category_id             BIGINT REFERENCES categories (id) ON DELETE SET NULL,
    name                    VARCHAR(255) NOT NULL,
    slug                    VARCHAR(280) NOT NULL UNIQUE,
    sku                     VARCHAR(80) NOT NULL UNIQUE,
    type                    VARCHAR(20) NOT NULL DEFAULT 'apparel', -- apparel|object|accessory
    status                  VARCHAR(20) NOT NULL DEFAULT 'draft', -- draft|published|archived|sold_out
    description             TEXT,
    short_description       VARCHAR(500),
    price                   DECIMAL(12, 2) NOT NULL,
    compare_at_price        DECIMAL(12, 2),
    cost_price              DECIMAL(12, 2),
    condition_grade         VARCHAR(20) NOT NULL DEFAULT 'good', -- mint|excellent|good|fair|poor
    era_decade              VARCHAR(20), -- e.g. 1970s, 1990s
    size_label              VARCHAR(50),
    color                   VARCHAR(80),
    material                VARCHAR(150),
    is_unique_piece         BOOLEAN NOT NULL DEFAULT FALSE,
    quantity_available      INTEGER NOT NULL DEFAULT 0,
    quantity_reserved       INTEGER NOT NULL DEFAULT 0,
    low_stock_threshold     SMALLINT NOT NULL DEFAULT 1,
    meta_title              VARCHAR(255),
    meta_description        VARCHAR(500),
    published_at            TIMESTAMPTZ,
    deleted_at              TIMESTAMPTZ,
    created_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_products_brand_id ON products (brand_id);
CREATE INDEX idx_products_category_id ON products (category_id);
CREATE INDEX idx_products_status ON products (status);
CREATE INDEX idx_products_type ON products (type);
CREATE INDEX idx_products_published_at ON products (published_at);
CREATE INDEX idx_products_deleted_at ON products (deleted_at);

-- -----------------------------------------------------------------------------
-- product_images — Product gallery
-- -----------------------------------------------------------------------------
CREATE TABLE product_images (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    path            VARCHAR(500) NOT NULL,
    alt             VARCHAR(255),
    sort_order      SMALLINT NOT NULL DEFAULT 0,
    is_primary      BOOLEAN NOT NULL DEFAULT FALSE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_product_images_product_id ON product_images (product_id);

-- -----------------------------------------------------------------------------
-- product_attributes — Flexible EAV for vintage-specific metadata
-- -----------------------------------------------------------------------------
CREATE TABLE product_attributes (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    key             VARCHAR(100) NOT NULL,
    value           VARCHAR(500) NOT NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_product_attributes_product_id ON product_attributes (product_id);
CREATE UNIQUE INDEX idx_product_attributes_product_key ON product_attributes (product_id, key);

-- -----------------------------------------------------------------------------
-- inventory_movements — Stock audit trail (append-only)
-- -----------------------------------------------------------------------------
CREATE TABLE inventory_movements (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    user_id         UUID REFERENCES users (id) ON DELETE SET NULL,
    type            VARCHAR(20) NOT NULL, -- stock_in|stock_out|reserve|release|adjust|return
    quantity        INTEGER NOT NULL,
    reference_type  VARCHAR(100),
    reference_id    VARCHAR(100),
    notes           TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_inventory_movements_product_id ON inventory_movements (product_id);
CREATE INDEX idx_inventory_movements_type ON inventory_movements (type);

-- -----------------------------------------------------------------------------
-- carts — Guest and authenticated shopping carts
-- -----------------------------------------------------------------------------
CREATE TABLE carts (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id         UUID REFERENCES users (id) ON DELETE CASCADE,
    session_id      VARCHAR(100),
    deleted_at      TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_carts_user_id ON carts (user_id);
CREATE INDEX idx_carts_session_id ON carts (session_id);

-- -----------------------------------------------------------------------------
-- cart_items — Line items with price snapshot
-- -----------------------------------------------------------------------------
CREATE TABLE cart_items (
    id              BIGSERIAL PRIMARY KEY,
    cart_id         UUID NOT NULL REFERENCES carts (id) ON DELETE CASCADE,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    quantity        SMALLINT NOT NULL DEFAULT 1,
    unit_price      DECIMAL(12, 2) NOT NULL,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (cart_id, product_id)
);

-- -----------------------------------------------------------------------------
-- orders — Checkout records with address snapshots
-- -----------------------------------------------------------------------------
CREATE TABLE orders (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id             UUID NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    number              VARCHAR(30) NOT NULL UNIQUE,
    status              VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending|paid|processing|shipped|delivered|cancelled|refunded
    currency            VARCHAR(3) NOT NULL DEFAULT 'USD',
    subtotal            DECIMAL(12, 2) NOT NULL DEFAULT 0,
    discount_total      DECIMAL(12, 2) NOT NULL DEFAULT 0,
    shipping_total      DECIMAL(12, 2) NOT NULL DEFAULT 0,
    tax_total           DECIMAL(12, 2) NOT NULL DEFAULT 0,
    grand_total         DECIMAL(12, 2) NOT NULL DEFAULT 0,
    coupon_code         VARCHAR(50),
    billing_address     JSONB,
    shipping_address    JSONB,
    notes               TEXT,
    placed_at           TIMESTAMPTZ,
    deleted_at          TIMESTAMPTZ,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_orders_user_id ON orders (user_id);
CREATE INDEX idx_orders_status ON orders (status);
CREATE INDEX idx_orders_placed_at ON orders (placed_at);

-- -----------------------------------------------------------------------------
-- order_items — Immutable line-item snapshots
-- product_id nullable when product is deleted post-purchase
-- -----------------------------------------------------------------------------
CREATE TABLE order_items (
    id              BIGSERIAL PRIMARY KEY,
    order_id        UUID NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
    product_id      UUID REFERENCES products (id) ON DELETE SET NULL,
    name            VARCHAR(255) NOT NULL,
    sku             VARCHAR(80) NOT NULL,
    quantity        SMALLINT NOT NULL DEFAULT 1,
    unit_price      DECIMAL(12, 2) NOT NULL,
    line_total      DECIMAL(12, 2) NOT NULL,
    meta            JSONB,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_order_items_order_id ON order_items (order_id);

-- -----------------------------------------------------------------------------
-- payments — Payment provider records
-- -----------------------------------------------------------------------------
CREATE TABLE payments (
    id                      BIGSERIAL PRIMARY KEY,
    order_id                UUID NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
    provider                VARCHAR(50) NOT NULL,
    status                  VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending|authorized|captured|failed|refunded
    amount                  DECIMAL(12, 2) NOT NULL,
    transaction_reference   VARCHAR(255),
    payload                 JSONB,
    created_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_payments_order_id ON payments (order_id);
CREATE INDEX idx_payments_status ON payments (status);

-- -----------------------------------------------------------------------------
-- shipments — Fulfillment tracking
-- -----------------------------------------------------------------------------
CREATE TABLE shipments (
    id                  BIGSERIAL PRIMARY KEY,
    order_id            UUID NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
    carrier             VARCHAR(100),
    tracking_number     VARCHAR(150),
    status              VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending|shipped|in_transit|delivered|returned|failed
    shipped_at          TIMESTAMPTZ,
    delivered_at        TIMESTAMPTZ,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_shipments_order_id ON shipments (order_id);

-- -----------------------------------------------------------------------------
-- coupons — Promotional discounts
-- -----------------------------------------------------------------------------
CREATE TABLE coupons (
    id                  BIGSERIAL PRIMARY KEY,
    code                VARCHAR(50) NOT NULL UNIQUE,
    type                VARCHAR(20) NOT NULL, -- percent|fixed
    value               DECIMAL(12, 2) NOT NULL,
    min_order_amount    DECIMAL(12, 2),
    max_uses            INTEGER,
    used_count          INTEGER NOT NULL DEFAULT 0,
    starts_at           TIMESTAMPTZ,
    ends_at             TIMESTAMPTZ,
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- wishlists — Saved items for users or guest sessions
-- -----------------------------------------------------------------------------
CREATE TABLE wishlists (
    id              BIGSERIAL PRIMARY KEY,
    user_id         UUID REFERENCES users (id) ON DELETE CASCADE,
    session_id      VARCHAR(100),
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_wishlists_user_id ON wishlists (user_id);
CREATE INDEX idx_wishlists_session_id ON wishlists (session_id);

-- -----------------------------------------------------------------------------
-- wishlist_items
-- -----------------------------------------------------------------------------
CREATE TABLE wishlist_items (
    id              BIGSERIAL PRIMARY KEY,
    wishlist_id     BIGINT NOT NULL REFERENCES wishlists (id) ON DELETE CASCADE,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (wishlist_id, product_id)
);

-- -----------------------------------------------------------------------------
-- reviews — Product ratings (moderated)
-- -----------------------------------------------------------------------------
CREATE TABLE reviews (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    user_id         UUID NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    rating          SMALLINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title           VARCHAR(150),
    body            TEXT,
    is_approved     BOOLEAN NOT NULL DEFAULT FALSE,
    deleted_at      TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_reviews_product_id ON reviews (product_id);
CREATE INDEX idx_reviews_user_id ON reviews (user_id);

-- -----------------------------------------------------------------------------
-- product_relations — Related, upsell, cross-sell, similar
-- -----------------------------------------------------------------------------
CREATE TABLE product_relations (
    id                  BIGSERIAL PRIMARY KEY,
    product_id          UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    related_product_id  UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    type                VARCHAR(20) NOT NULL, -- related|upsell|cross_sell|similar
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (product_id, related_product_id, type)
);

-- -----------------------------------------------------------------------------
-- product_views — Analytics events
-- -----------------------------------------------------------------------------
CREATE TABLE product_views (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    user_id         UUID REFERENCES users (id) ON DELETE SET NULL,
    session_id      VARCHAR(100),
    ip_address      VARCHAR(45),
    viewed_at       TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_product_views_product_id ON product_views (product_id);
CREATE INDEX idx_product_views_viewed_at ON product_views (viewed_at);

-- -----------------------------------------------------------------------------
-- settings — Application key-value configuration
-- -----------------------------------------------------------------------------
CREATE TABLE settings (
    id              BIGSERIAL PRIMARY KEY,
    key             VARCHAR(150) NOT NULL UNIQUE,
    value           JSONB NOT NULL DEFAULT '{}',
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
