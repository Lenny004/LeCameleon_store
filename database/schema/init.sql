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
    measurements            JSONB,
    is_authenticated        BOOLEAN NOT NULL DEFAULT FALSE,
    authenticity_notes      TEXT,
    authenticated_at        TIMESTAMPTZ,
    authenticated_by        UUID REFERENCES users (id) ON DELETE SET NULL,
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
-- orders — Checkout records with address snapshots (user_id nullable for guest checkout)
-- -----------------------------------------------------------------------------
CREATE TABLE orders (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id             UUID REFERENCES users (id) ON DELETE RESTRICT,
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
-- stock_alerts — Waitlist for out-of-stock products
-- -----------------------------------------------------------------------------
CREATE TABLE stock_alerts (
    id              BIGSERIAL PRIMARY KEY,
    product_id      UUID NOT NULL REFERENCES products (id) ON DELETE CASCADE,
    email           VARCHAR(255) NOT NULL,
    user_id         UUID REFERENCES users (id) ON DELETE SET NULL,
    notified_at     TIMESTAMPTZ,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (product_id, email)
);

CREATE INDEX idx_stock_alerts_product_id ON stock_alerts (product_id);

-- -----------------------------------------------------------------------------
-- return_requests — Customer return submissions (status only)
-- -----------------------------------------------------------------------------
CREATE TABLE return_requests (
    id              BIGSERIAL PRIMARY KEY,
    order_id        UUID NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
    order_item_id   BIGINT REFERENCES order_items (id) ON DELETE SET NULL,
    user_id         UUID REFERENCES users (id) ON DELETE SET NULL,
    reason          TEXT NOT NULL,
    status          VARCHAR(20) NOT NULL DEFAULT 'pending', -- pending|approved|denied|refunded
    admin_notes     TEXT,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_return_requests_order_id ON return_requests (order_id);
CREATE INDEX idx_return_requests_status ON return_requests (status);

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

-- -----------------------------------------------------------------------------
-- sv_departments — El Salvador departments (14, post-2024 reform)
-- -----------------------------------------------------------------------------
CREATE TABLE sv_departments (
    id              BIGSERIAL PRIMARY KEY,
    code            VARCHAR(10) NOT NULL UNIQUE,
    name            VARCHAR(120) NOT NULL,
    slug            VARCHAR(140) NOT NULL UNIQUE,
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- sv_municipalities — 44 consolidated municipalities with flat shipping rates
-- -----------------------------------------------------------------------------
CREATE TABLE sv_municipalities (
    id                  BIGSERIAL PRIMARY KEY,
    sv_department_id    BIGINT NOT NULL REFERENCES sv_departments (id) ON DELETE CASCADE,
    code                VARCHAR(20) NOT NULL UNIQUE,
    name                VARCHAR(150) NOT NULL,
    slug                VARCHAR(180) NOT NULL UNIQUE,
    region_label        VARCHAR(20) NOT NULL, -- Norte|Sur|Este|Oeste|Centro|Costa
    base_shipping_cost  NUMERIC(10, 2) NOT NULL DEFAULT 0,
    latitude            NUMERIC(10, 7),
    longitude           NUMERIC(10, 7),
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_sv_municipalities_department ON sv_municipalities (sv_department_id);
CREATE INDEX idx_sv_municipalities_region ON sv_municipalities (region_label);

-- -----------------------------------------------------------------------------
-- sv_districts — Former municipalities (262 total; seed samples only)
-- -----------------------------------------------------------------------------
CREATE TABLE sv_districts (
    id                  BIGSERIAL PRIMARY KEY,
    sv_municipality_id  BIGINT NOT NULL REFERENCES sv_municipalities (id) ON DELETE CASCADE,
    name                VARCHAR(150) NOT NULL,
    slug                VARCHAR(180) NOT NULL,
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (sv_municipality_id, slug)
);

-- -----------------------------------------------------------------------------
-- logistics_companies — Carrier / partner brands
-- -----------------------------------------------------------------------------
CREATE TABLE logistics_companies (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name                VARCHAR(150) NOT NULL,
    legal_name          VARCHAR(200),
    trade_name          VARCHAR(150),
    tax_id              VARCHAR(30),
    email               VARCHAR(150),
    phone               VARCHAR(30),
    address_line        VARCHAR(255),
    sv_municipality_id  BIGINT REFERENCES sv_municipalities (id) ON DELETE SET NULL,
    website             VARCHAR(255),
    logo_path           VARCHAR(500),
    contact_person      VARCHAR(150),
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    notes               TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at          TIMESTAMPTZ
);

-- -----------------------------------------------------------------------------
-- logistics_workers — Collectors, drivers, dispatchers
-- -----------------------------------------------------------------------------
CREATE TABLE logistics_workers (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    logistics_company_id    UUID REFERENCES logistics_companies (id) ON DELETE SET NULL,
    user_id                 UUID REFERENCES users (id) ON DELETE SET NULL,
    employee_code           VARCHAR(30),
    first_name              VARCHAR(100) NOT NULL,
    last_name               VARCHAR(100) NOT NULL,
    document_id             VARCHAR(20),
    phone                   VARCHAR(30),
    email                   VARCHAR(150),
    role                    VARCHAR(20) NOT NULL, -- collector|driver|dispatcher|supervisor
    hire_date               DATE,
    is_active               BOOLEAN NOT NULL DEFAULT TRUE,
    notes                   TEXT,
    created_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at              TIMESTAMPTZ
);

CREATE INDEX idx_logistics_workers_role ON logistics_workers (role);

-- -----------------------------------------------------------------------------
-- logistics_vehicles — Fleet units assigned to companies
-- -----------------------------------------------------------------------------
CREATE TABLE logistics_vehicles (
    id                      UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    logistics_company_id    UUID NOT NULL REFERENCES logistics_companies (id) ON DELETE CASCADE,
    logistics_worker_id     UUID REFERENCES logistics_workers (id) ON DELETE SET NULL,
    plate_number            VARCHAR(20) NOT NULL UNIQUE,
    brand                   VARCHAR(80),
    model                   VARCHAR(80),
    year                    SMALLINT,
    color                   VARCHAR(40),
    vehicle_type            VARCHAR(20) NOT NULL, -- motorcycle|van|truck|bicycle|car
    capacity_kg             NUMERIC(8, 2),
    is_active               BOOLEAN NOT NULL DEFAULT TRUE,
    notes                   TEXT,
    created_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_logistics_vehicles_type ON logistics_vehicles (vehicle_type);

-- -----------------------------------------------------------------------------
-- shipping_zones — Delivery zones (may map 1:1 to a municipality)
-- -----------------------------------------------------------------------------
CREATE TABLE shipping_zones (
    id                  BIGSERIAL PRIMARY KEY,
    code                VARCHAR(30) NOT NULL UNIQUE,
    name                VARCHAR(150) NOT NULL,
    description         TEXT,
    sv_municipality_id  BIGINT REFERENCES sv_municipalities (id) ON DELETE SET NULL,
    sort_order          SMALLINT NOT NULL DEFAULT 0,
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- shipping_zone_rates — Origin-to-destination zone pricing
-- -----------------------------------------------------------------------------
CREATE TABLE shipping_zone_rates (
    id                      BIGSERIAL PRIMARY KEY,
    origin_zone_id          BIGINT NOT NULL REFERENCES shipping_zones (id) ON DELETE CASCADE,
    destination_zone_id   BIGINT NOT NULL REFERENCES shipping_zones (id) ON DELETE CASCADE,
    base_fee                NUMERIC(10, 2) NOT NULL,
    per_km_fee              NUMERIC(10, 2) NOT NULL DEFAULT 0,
    min_fee                 NUMERIC(10, 2) NOT NULL DEFAULT 0,
    max_fee                 NUMERIC(10, 2),
    estimated_hours         NUMERIC(6, 2),
    is_active               BOOLEAN NOT NULL DEFAULT TRUE,
    created_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at              TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (origin_zone_id, destination_zone_id)
);

-- -----------------------------------------------------------------------------
-- dispatch_schedules — Next pickup / dispatch windows
-- -----------------------------------------------------------------------------
CREATE TABLE dispatch_schedules (
    id                  BIGSERIAL PRIMARY KEY,
    name                VARCHAR(150) NOT NULL,
    next_dispatch_at    TIMESTAMPTZ NOT NULL,
    cutoff_at           TIMESTAMPTZ NOT NULL,
    is_active           BOOLEAN NOT NULL DEFAULT TRUE,
    notes               TEXT,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- delivery_warnings — Checkout / tracking / admin notices
-- -----------------------------------------------------------------------------
CREATE TABLE delivery_warnings (
    id              BIGSERIAL PRIMARY KEY,
    code            VARCHAR(50) NOT NULL UNIQUE,
    title           VARCHAR(200) NOT NULL,
    body            TEXT NOT NULL,
    severity        VARCHAR(20) NOT NULL, -- info|warning|danger
    applies_to      VARCHAR(20) NOT NULL, -- checkout|tracking|admin
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at      TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- -----------------------------------------------------------------------------
-- shipment_events — Shipment lifecycle and recipient outcome log
-- -----------------------------------------------------------------------------
CREATE TABLE shipment_events (
    id                  BIGSERIAL PRIMARY KEY,
    shipment_id         BIGINT NOT NULL REFERENCES shipments (id) ON DELETE CASCADE,
    status              VARCHAR(30) NOT NULL,
    recipient_outcome   VARCHAR(30), -- accepted|refused|no_answer|wrong_address|rescheduled|left_with_neighbor
    note                TEXT,
    happened_at         TIMESTAMPTZ NOT NULL,
    created_by          UUID REFERENCES users (id) ON DELETE SET NULL,
    created_at          TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_shipment_events_shipment ON shipment_events (shipment_id, happened_at);

-- -----------------------------------------------------------------------------
-- shipments (logistics extensions) — columns added via migration 2026_07_15_000016
-- logistics_company_id, logistics_worker_id, logistics_vehicle_id,
-- origin_municipality_id, destination_municipality_id, shipping_zone_rate_id,
-- quoted_fee, distance_km, recipient_name, recipient_phone, recipient_notes,
-- next_attempt_at; status widened to VARCHAR(30)
-- -----------------------------------------------------------------------------
-- coupons.shipping_only — BOOLEAN DEFAULT FALSE (migration 2026_07_15_000017)
