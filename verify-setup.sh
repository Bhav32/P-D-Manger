#!/bin/bash
# P&D Manager - Full Stack Application Setup Script
# Run this script after migrations to verify everything is set up correctly

echo "================================"
echo "P&D Manager - Setup Verification"
echo "================================"
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check backend
echo -e "${BLUE}Checking Backend Setup...${NC}"
if [ -f "backend/.env" ]; then
    echo -e "${GREEN}✓${NC} .env file found"
else
    echo -e "${RED}✗${NC} .env file missing"
fi

if [ -d "backend/app/Http/Controllers" ]; then
    if [ -f "backend/app/Http/Controllers/AuthController.php" ]; then
        echo -e "${GREEN}✓${NC} AuthController created"
    else
        echo -e "${RED}✗${NC} AuthController missing"
    fi
fi

if [ -d "backend/app/Models" ]; then
    echo -e "${GREEN}✓${NC} Models directory exists"
    [ -f "backend/app/Models/User.php" ] && echo -e "  ${GREEN}✓${NC} User model" || echo -e "  ${RED}✗${NC} User model"
    [ -f "backend/app/Models/Product.php" ] && echo -e "  ${GREEN}✓${NC} Product model" || echo -e "  ${RED}✗${NC} Product model"
    [ -f "backend/app/Models/Discount.php" ] && echo -e "  ${GREEN}✓${NC} Discount model" || echo -e "  ${RED}✗${NC} Discount model"
fi

echo ""
echo -e "${BLUE}Checking Migrations...${NC}"
if [ -d "backend/database/migrations" ]; then
    migration_count=$(ls -1 backend/database/migrations/*.php 2>/dev/null | wc -l)
    echo -e "${GREEN}✓${NC} Found $migration_count migration files"
fi

echo ""
echo -e "${BLUE}Checking Seeders...${NC}"
if [ -d "backend/database/seeders" ]; then
    seeder_files=$(ls -1 backend/database/seeders/*.php 2>/dev/null | grep -c Seeder || echo 0)
    echo -e "${GREEN}✓${NC} Found seeders"
fi

echo ""
echo -e "${BLUE}Checking Frontend Setup...${NC}"
if [ -d "frontend/src/app/components/login" ]; then
    echo -e "${GREEN}✓${NC} Login component directory exists"
    [ -f "frontend/src/app/components/login/login.component.ts" ] && echo -e "  ${GREEN}✓${NC} login.component.ts" || echo -e "  ${RED}✗${NC} login.component.ts"
    [ -f "frontend/src/app/components/login/login.component.html" ] && echo -e "  ${GREEN}✓${NC} login.component.html" || echo -e "  ${RED}✗${NC} login.component.html"
    [ -f "frontend/src/app/components/login/login.component.css" ] && echo -e "  ${GREEN}✓${NC} login.component.css" || echo -e "  ${RED}✗${NC} login.component.css"
fi

if [ -f "frontend/src/app/services/auth.service.ts" ]; then
    echo -e "${GREEN}✓${NC} AuthService created"
fi

if [ -f "frontend/src/app/interceptors/jwt.interceptor.ts" ]; then
    echo -e "${GREEN}✓${NC} JwtInterceptor created"
fi

echo ""
echo -e "${BLUE}Checking Documentation...${NC}"
doc_files=("README.md" "SETUP_GUIDE.md" "QUICK_START.md" "API_REFERENCE.md" "DATABASE_SCHEMA.md" "IMPLEMENTATION_SUMMARY.md" "VERIFICATION_CHECKLIST.md" "PROJECT_SUMMARY.md")
for doc in "${doc_files[@]}"; do
    if [ -f "$doc" ]; then
        size=$(du -h "$doc" | cut -f1)
        echo -e "${GREEN}✓${NC} $doc ($size)"
    else
        echo -e "${RED}✗${NC} $doc"
    fi
done

echo ""
echo "================================"
echo "Setup Verification Complete!"
echo "================================"
echo ""
echo "Next steps:"
echo "1. cd backend && php artisan migrate:fresh --seed"
echo "2. cd backend && php artisan serve"
echo "3. cd frontend && npm install && npm start"
echo "4. Visit http://localhost:4200/login"
echo ""
